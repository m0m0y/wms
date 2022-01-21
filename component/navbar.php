<nav id="navbar-wms">
    <img src="static/css/font/<?= (isset($_COOKIE['wms-dark'])) ? 'logo-light' : 'logo' ?>.png" alt="Progressive Medical Corporation" />
    <a href="#!" class="sidebar-trigger"><i class="material-icons" id="side-icon">menu</i></a>
    <a href="#!" class="navbar-search"><i class="material-icons">search</i></a>
</nav>

<div class="nav-fix-sace"></div>


<div class="user-control">
    <a class="side-toggler" href="#!"><i class="material-icons" id="side-toggle" title="Toggle Sidebar"><?= (isset($_COOKIE['side-off'])) ? 'menu' : 'short_text' ?></i></a>
    <input id="slip-search" type="text" placeholder="Search slip number" autocomplete="off"/>
    <a class="logout" href="logout.php">Logout<i class="material-icons ml-3">exit_to_app</i></a>
    <!-- <a href="#!" title="Dark / Light Mode"><i class="material-icons ml-3 dark-toggler"><?= (isset($_COOKIE['wms-dark'])) ? 'nights_stay' : 'brightness_high' ?></i></a> -->
</div>

<div id="slip-result" class="shadow">
    <ul class="list-group rounded-0" id="slip-list">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <a href="#!">Slip number not found</a>
        </li>
    </ul>
</div>