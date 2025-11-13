<?php
session_name("admin_session");
session_start();

// Incluir sistema de logs
require_once 'logger.php';
require_once __DIR__ . '/../utils/lang.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
$mensaje = "";
$error = false;

// Selector de idioma (persistido en sesión admin)
if (isset($_POST['setlang']) && isset($_POST['lang'])) {
    $newLang = $_POST['lang'];
    if (in_array($newLang, pt_supported_langs(), true)) {
        $_SESSION['lang'] = $newLang;
    }
    header('Location: create_sentence.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //me cargo la frase resaltada anterior para no duplicar y generar una nueva sin confusión
    unset($_SESSION['ultima_frase']);
    unset($_SESSION['ultim_nivell']);
    $nivell = $_POST['nivell'] ?? '';
    $frase = trim($_POST['frase'] ?? '');
    
    if ($nivell === '' || $frase === '') {
        $mensaje = t('admin.create.errors.required');
        $error = true;
    } else {
        $lang = pt_current_lang();
        $archivo = __DIR__ . '/../frases.' . $lang . '.txt';

        if (!file_exists($archivo)) {
            $frases = [
                'facil' => [],
                'normal' => [],
                'dificil' => []
            ];
        } else {
            $contenido = file_get_contents($archivo);
            $frases = json_decode($contenido, true);

            if ($frases === null) {
                $mensaje = t('admin.create.errors.file_malformed');
                $error = true;
            }
        }
        if (!$error) {
            if (!isset($frases[$nivell])) {
                $frases[$nivell] = [];
            }
            //========MANEJO DE IMAGENES SUBIDAS=========
            $nombreImagen = null; // Por defecto sin imagen
    
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                
                $nombreImagen = $_FILES['imagen']['name'];
                $rutaDestino = '../images/' . $nombreImagen;
                
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                    $mensaje = t('admin.create.errors.image_error');
                    $error = true;
                    $nombreImagen = null;
                }
               
            }
            // Crear objeto frase con texto e imagen 
            $nuevaFrase = [
                'texto' => $frase,
                'imagen' => $nombreImagen 
            ];
            
            $frases[$nivell][] = $nuevaFrase;

            $json_nuevo = json_encode($frases, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($archivo, $json_nuevo) === false) {
                $mensaje = t('admin.create.errors.save_error');
                $error = true;
            } else {
                $mensaje = t('admin.create.submit_add');
                // Log creación exitosa de frase
                $imagenInfo = $nombreImagen ? " con imagen: $nombreImagen" : " sin imagen";
                logAdmin("CREATE_SENTENCE", "create_sentence.php", "Nueva frase creada en nivel '$nivell': '$frase'$imagenInfo");
                //me guardo la última frase y nivel para resaltarla al volver al listado!!!
                $_SESSION['ultima_frase'] = $frase;
                $_SESSION['ultim_nivell'] = $nivell;
                
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(pt_current_lang()); ?>">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars(t('admin.create.title')); ?></title>
        <link rel="stylesheet" href="../styles.css?<?php echo time(); ?>">
    </head>
    <body class="admin-page-index">
        <div class="admin-container-index">
            <p> <?= htmlspecialchars(t('admin.welcome')); ?>, <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong> | 
            <a href="logout.php" class="admin-link-btn logout" id="logout-link">
                <?= htmlspecialchars(t('admin.logout')); ?>
            </a>
            |
            <?php
            // Enlace al panel con el nivel correcto si existe, te redirecciona al listado del último nivel agregado
            $panel_url = 'index.php';
            if (isset($_SESSION['ultim_nivell'])) {
                $panel_url .= '?action=llistar&nivell=' . urlencode($_SESSION['ultim_nivell']);
            }
            ?>
            <a href="<?= $panel_url ?>" class="admin-link-btn" id="panel-link">
                <?= htmlspecialchars(t('admin.create.back_to_panel')); ?>
            </a>
            </p>

            <!-- Selector de idioma para Admin -->
            <form action="create_sentence.php" method="post" style="position:fixed; top:10px; left:10px; white-space:nowrap; z-index:9999;">
                <input type="hidden" name="setlang" value="1">
                <label for="lang" style="margin-right:6px;"><?= htmlspecialchars(t('admin.language_label')); ?></label>
                <?php $cur = pt_current_lang(); $names = pt_load_messages($cur)['lang_names'] ?? ['es'=>'Español','ca'=>'Català','en'=>'English']; ?>
                <select name="lang" id="lang" onchange="this.form.submit()">
                    <option value="es" <?= $cur==='es'?'selected':''; ?>><?= htmlspecialchars($names['es'] ?? 'Español'); ?></option>
                    <option value="ca" <?= $cur==='ca'?'selected':''; ?>><?= htmlspecialchars($names['ca'] ?? 'Català'); ?></option>
                    <option value="en" <?= $cur==='en'?'selected':''; ?>><?= htmlspecialchars($names['en'] ?? 'English'); ?></option>
                </select>
            </form>

            <h1><?= htmlspecialchars(t('admin.create.title')); ?></h1>
            <form action="create_sentence.php" method="POST" enctype="multipart/form-data" id="create-form">
                <label for="nivell"><?= htmlspecialchars(t('admin.create.difficulty_label')); ?></label>
                <select name="nivell" id="nivell" required>
                    <option value=""><?= htmlspecialchars(t('admin.create.difficulty_placeholder')); ?></option>
                    <option value="facil"><?= htmlspecialchars(t('index.difficulty_facil')); ?></option>
                    <option value="normal"><?= htmlspecialchars(t('index.difficulty_normal')); ?></option>
                    <option value="dificil"><?= htmlspecialchars(t('index.difficulty_dificil')); ?></option>
                </select>

                <label for="frase"><?= htmlspecialchars(t('admin.create.phrase_label')); ?></label>
                <textarea name="frase" id="frase" rows="4" required></textarea>
                
                <label for="imagen"><?= htmlspecialchars(t('admin.create.image_label')); ?></label>
                <input type="file" name="imagen" id="imagen" accept="image/*">
                

                <button type="submit" id="add-btn">
                    <?= htmlspecialchars(t('admin.create.submit_add')); ?>
                </button>

                <?php if ($mensaje): ?>
                    <div class="admin-message <?php echo $error ? 'error' : 'success'; ?>">
                        <?php echo $error ? '❌' : '✅'; ?> <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </body>
    <script>
        
        //esto es pa que no joda los atajos cuando el usuario está escribiendo
        
        document.addEventListener("keydown", function(event){
            if (document.activeElement.tagName === 'INPUT' || 
                document.activeElement.tagName === 'TEXTAREA' ||
                document.activeElement.tagName === 'SELECT' ||
                document.activeElement.isContentEditable ) {
                return; 
            }
            const teclita = event.key.toLowerCase();
            if (teclita === "l") {
                window.location.href = "logout.php";
            }
            if (teclita === "t") {
                window.location.href = "index.php";
                document.getElementById("panel-link").click();
            }
            if (teclita === "a") {
                document.getElementById("add-btn").click();
            }
        })

    </script>
</html>
