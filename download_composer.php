<?php
echo "📥 Téléchargement de Composer...\n";

// Télécharger composer.phar
$composerUrl = 'https://getcomposer.org/download/latest-stable/composer.phar';
$composerPath = 'composer.phar';

$composerContent = file_get_contents($composerUrl);
if ($composerContent === false) {
    echo "❌ Erreur de téléchargement\n";
    exit(1);
}

file_put_contents($composerPath, $composerContent);
echo "✅ Composer téléchargé: " . $composerPath . "\n";

// Test
echo "🧪 Test Composer...\n";
system('php composer.phar --version');
?>
