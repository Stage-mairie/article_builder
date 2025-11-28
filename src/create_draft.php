<?php
// Inclure WordPress pour accéder aux fonctions WP
require_once('/chemin/vers/wp-load.php'); // modifie le chemin selon ton installation

// Sécurité : vérifier méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Accès refusé');
}

// Récupérer les champs du formulaire
$title = sanitize_text_field($_POST['article-title']);
$content = sanitize_textarea_field($_POST['description']);
$theme = sanitize_text_field($_POST['theme']);
$keywords = sanitize_text_field($_POST['keywords']);
$categories = isset($_POST['categories']) ? $_POST['categories'] : [];

// Créer le brouillon
$post_id = wp_insert_post([
    'post_title'   => $title,
    'post_content' => $content,
    'post_status'  => 'draft',
    'post_type'    => 'post',
    'tags_input'   => $keywords,
]);

if (is_wp_error($post_id)) {
    die('Erreur création brouillon');
}

// Catégories : vérifier si elles existent ou les créer
foreach ($categories as $cat_name) {
    $cat = term_exists($cat_name, 'category');
    if (!$cat) {
        $cat_id = wp_create_category($cat_name);
    } else {
        $cat_id = $cat['term_id'];
    }
    wp_set_post_categories($post_id, [$cat_id], true);
}

// Upload image de couverture facultative
if (isset($_FILES['cover-image']) && $_FILES['cover-image']['size'] > 0) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $cover_id = media_handle_upload('cover-image', $post_id);
    if (!is_wp_error($cover_id)) {
        set_post_thumbnail($post_id, $cover_id);
    }
}

// Upload des PDFs (facultatif)
if (isset($_FILES['pdf-files'])) {
    foreach ($_FILES['pdf-files']['name'] as $key => $name) {
        if ($_FILES['pdf-files']['size'][$key] > 0) {
            $_FILES['upload_file'] = [
                'name'     => $name,
                'type'     => $_FILES['pdf-files']['type'][$key],
                'tmp_name' => $_FILES['pdf-files']['tmp_name'][$key],
                'error'    => $_FILES['pdf-files']['error'][$key],
                'size'     => $_FILES['pdf-files']['size'][$key],
            ];
            media_handle_upload('upload_file', $post_id);
        }
    }
}

echo "Brouillon créé avec succès !";
