</div> 

<?php if (isset($total_paginas) && $total_paginas > 1): ?>
<nav aria-label="Paginación de productos" class="mt-4">
    <ul class="pagination justify-content-center">
        
        <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $pagina_actual - 1; ?>&<?php echo $filtros_query; ?>">Anterior</a>
        </li>
        
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                <a class="page-link" href="?p=<?php echo $i; ?>&<?php echo $filtros_query; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?p=<?php echo $pagina_actual + 1; ?>&<?php echo $filtros_query; ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

</div> <footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?php echo date("Y"); ?> Mi Tienda E-commerce. Todos los derechos reservados.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#" class="text-white text-decoration-none">Acerca de</a></li>
                    <li class="list-inline-item">|</li>
                    <li class="list-inline-item"><a href="#" class="text-white text-decoration-none">Contacto</a></li>
                    <li class="list-inline-item">|</li>
                    <li class="list-inline-item"><a href="#" class="text-white text-decoration-none">Privacidad</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>