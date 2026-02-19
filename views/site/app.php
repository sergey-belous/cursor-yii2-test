<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

$this->title = 'Сервис коротких ссылок + QR';

$manifestPath = Yii::getAlias('@webroot/dist/.vite/manifest.json');
$legacyManifestPath = Yii::getAlias('@webroot/dist/manifest.json');
$entryKey = 'src/main.js';
$resolvedManifestPath = null;
if (is_file($manifestPath)) {
    $resolvedManifestPath = $manifestPath;
} elseif (is_file($legacyManifestPath)) {
    $resolvedManifestPath = $legacyManifestPath;
}

$hasProductionAssets = $resolvedManifestPath !== null;
$enableDevServer = YII_ENV_DEV && !$hasProductionAssets;
$devServerUrl = rtrim((string) getenv('VITE_DEV_SERVER_URL') ?: 'http://localhost:5173', '/');

if ($hasProductionAssets) {
    $manifest = Json::decode(file_get_contents($resolvedManifestPath));
    $entry = $manifest[$entryKey] ?? null;

    if (is_array($entry) && isset($entry['file'])) {
        $cssFiles = [];
        $visited = [];

        $collectCss = static function (string $chunkKey) use (&$collectCss, &$cssFiles, &$visited, $manifest): void {
            if (isset($visited[$chunkKey]) || !isset($manifest[$chunkKey]) || !is_array($manifest[$chunkKey])) {
                return;
            }

            $visited[$chunkKey] = true;
            $chunk = $manifest[$chunkKey];

            foreach ($chunk['css'] ?? [] as $cssFile) {
                $cssFiles[$cssFile] = true;
            }

            foreach ($chunk['imports'] ?? [] as $importChunk) {
                $collectCss($importChunk);
            }
        };

        $collectCss($entryKey);

        foreach (array_keys($cssFiles) as $cssFile) {
            $this->registerCssFile("@web/dist/{$cssFile}");
        }

        $this->registerJsFile(
            "@web/dist/{$entry['file']}",
            ['type' => 'module', 'position' => View::POS_END]
        );
    }
}

if ($enableDevServer) {
    $this->registerJsFile("{$devServerUrl}/@vite/client", ['type' => 'module', 'position' => View::POS_HEAD]);
    $this->registerJsFile("{$devServerUrl}/src/main.js", ['type' => 'module', 'position' => View::POS_END]);
}
?>

<div id="app"></div>

<?php if (!$hasProductionAssets && !$enableDevServer): ?>
    <div class="container py-5">
        <div class="alert alert-warning">
            <?= Html::encode('Frontend assets are missing. Build Vue app in frontend/ directory.') ?>
        </div>
    </div>
<?php endif; ?>
