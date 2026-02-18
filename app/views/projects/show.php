<?php
$techIconMap = [
  'php' => 'devicon-php-plain',
  'mysql' => 'devicon-mysql-plain',
  'javascript' => 'devicon-javascript-plain',
  'js' => 'devicon-javascript-plain',
  'html' => 'devicon-html5-plain',
  'css' => 'devicon-css3-plain',
  'git' => 'devicon-git-plain',
  'go' => 'devicon-go-plain',
  'python' => 'devicon-python-plain',
  'laravel' => 'devicon-laravel-plain',
  'docker' => 'devicon-docker-plain',
  'react' => 'devicon-react-original',
  'node' => 'devicon-nodejs-plain',
  'nodejs' => 'devicon-nodejs-plain',
  'java' => 'devicon-java-plain',
  'c#' => 'devicon-csharp-plain',
  'c++' => 'devicon-cplusplus-plain',
  'typescript' => 'devicon-typescript-plain',
  'postgresql' => 'devicon-postgresql-plain',
];
?>
<div class="card card-pad detail-shell">
  <?php $isAdmin = class_exists('Auth') && Auth::check(); ?>

  <div class="row">
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <h1 style="margin:0;"><?= htmlspecialchars($heading) ?></h1>

      <?php $status = $status ?? 'pending'; ?>
      <span class="badge badge--<?= htmlspecialchars($status) ?>">
        <span class="badge-dot"></span>
        <?= htmlspecialchars(ucfirst($status)) ?>
      </span>
    </div>

    <div class="muted">
      <strong>ID:</strong> <?= htmlspecialchars((string)($id ?? '')) ?>
    </div>
  </div>

  <p style="margin-top:12px;">
    <?= nl2br(htmlspecialchars($description)) ?>
  </p>

  <div class="muted detail-meta"><b>Tecnologías:</b></div>
  <div class="tech-list-inline">
    <?php if (!isset($techNames) || empty($techNames)): ?>
      <span class="tech-pill">Pendiente</span>
    <?php else: ?>
      <?php foreach ($techNames as $techName): ?>
        <?php
        $techName = (string)$techName;
        $iconClass = $techIconMap[mb_strtolower(trim($techName))] ?? null;
        ?>
        <span class="tech-pill">
          <?php if ($iconClass): ?><i class="<?= htmlspecialchars($iconClass) ?>" aria-hidden="true"></i><?php else: ?>•<?php endif; ?>
          <?= htmlspecialchars($techName) ?>
        </span>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap;">
    <a class="btn btn-primary" href="/projects/open/<?= urlencode((string)($id ?? "")) ?>">Abrir proyecto</a>
  </div>

  <div style="margin-top:16px; display:flex; gap:8px; flex-wrap:wrap;">
    <?php if ($isAdmin): ?>
      <a class="btn" href="/projects/edit/<?= urlencode((string)($id ?? '')) ?>">Editar</a>
    <?php endif; ?>
    <a class="btn" href="/projects">← Volver a proyectos</a>
  </div>
</div>
