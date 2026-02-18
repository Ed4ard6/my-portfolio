<?php
$statusLabels = [
  'pending' => 'Pendiente',
  'active' => 'Activo',
  'completed' => 'Completado',
  'archived' => 'Archivado',
];
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

<section class="card card-pad page-shell">
  <h1 style="margin-top:0;"><?= htmlspecialchars($heading ?? 'Proyectos') ?></h1>
  <p><?= htmlspecialchars($description ?? '') ?></p>

  <hr>

  <?php $isAdmin = class_exists('Auth') && Auth::check(); ?>

  <?php if ($isAdmin): ?>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin:12px 0;">
      <a class="btn btn-primary" href="/projects/create">➕ Crear proyecto</a>
      <a class="btn btn-secondary" href="/projects/archived">Ver archivados</a>
    </div>
  <?php endif; ?>

  <?php $currentStatus = $currentStatus ?? ''; ?>

  <div style="display:flex; gap:10px; flex-wrap:wrap; margin:12px 0;">
    <a class="btn <?= $currentStatus === '' ? 'btn-primary' : '' ?>" href="/projects">Todos</a>
    <a class="btn <?= $currentStatus === 'pending' ? 'btn-primary' : '' ?>" href="/projects?status=pending">Pendiente</a>
    <a class="btn <?= $currentStatus === 'active' ? 'btn-primary' : '' ?>" href="/projects?status=active">Activo</a>
    <a class="btn <?= $currentStatus === 'completed' ? 'btn-primary' : '' ?>" href="/projects?status=completed">Completado</a>
  </div>

  <?php if (empty($projects)): ?>
    <p>No hay proyectos aún. Crea el primero 👆</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($projects as $p): ?>
        <?php $status = $p['status'] ?? 'pending'; ?>

        <div class="card card-pad">
          <div class="row">
            <div>
              <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <strong><?= htmlspecialchars($p['name']) ?></strong>

                <span class="badge badge--<?= htmlspecialchars($status) ?>">
                  <span class="badge-dot"></span>
                  <?= htmlspecialchars($statusLabels[$status] ?? ucfirst($status)) ?>
                </span>
              </div>

              <div class="muted detail-meta"><b>Tecnologías:</b></div>
              <div class="tech-list-inline">
                <?php
                $techs = array_filter(array_map('trim', explode(',', (string)($p['technologies'] ?? ''))));
                ?>
                <?php if (empty($techs)): ?>
                  <span class="tech-pill">Pendiente</span>
                <?php else: ?>
                  <?php foreach ($techs as $tech): ?>
                    <?php $iconClass = $techIconMap[mb_strtolower($tech)] ?? null; ?>
                    <span class="tech-pill">
                      <?php if ($iconClass): ?><i class="<?= htmlspecialchars($iconClass) ?>" aria-hidden="true"></i><?php else: ?>•<?php endif; ?>
                      <?= htmlspecialchars($tech) ?>
                    </span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="card-actions">
              <a class="btn" href="/projects/show/<?= urlencode((string)$p['id']) ?>">Ver detalle</a>
              <a class="btn btn-primary" href="/projects/open/<?= urlencode((string)$p['id']) ?>">Abrir proyecto</a>

              <?php if ($isAdmin): ?>
                <a class="btn" href="/projects/edit/<?= urlencode((string)$p['id']) ?>">Editar</a>
                <a class="btn btn-danger"
                  href="/projects/archive/<?= urlencode((string)$p['id']) ?>"
                  onclick="return confirm('¿Seguro que quieres archivar este proyecto?');">
                  Archivar
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php
    $pagination = $pagination ?? ['page' => 1, 'totalPages' => 1, 'total' => count($projects ?? [])];
    $page = (int)($pagination['page'] ?? 1);
    $totalPages = (int)($pagination['totalPages'] ?? 1);

    $buildUrl = static function (int $targetPage, string $currentStatus): string {
      $query = [];
      if ($currentStatus !== '') {
        $query['status'] = $currentStatus;
      }
      if ($targetPage > 1) {
        $query['page'] = $targetPage;
      }
      $qs = http_build_query($query);
      return '/projects' . ($qs !== '' ? ('?' . $qs) : '');
    };
  ?>

  <?php if ($totalPages > 1): ?>
    <div class="pagination-wrap">
      <a class="btn" href="<?= htmlspecialchars($buildUrl(max(1, $page - 1), (string)$currentStatus)) ?>" <?= $page <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>← Anterior</a>
      <div class="pagination-pages">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a class="btn <?= $i === $page ? 'btn-primary' : '' ?>" href="<?= htmlspecialchars($buildUrl($i, (string)$currentStatus)) ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
      <a class="btn" href="<?= htmlspecialchars($buildUrl(min($totalPages, $page + 1), (string)$currentStatus)) ?>" <?= $page >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>Siguiente →</a>
    </div>
  <?php endif; ?>
</section>
