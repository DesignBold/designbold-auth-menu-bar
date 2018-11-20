<!-- Designit header -->
<?php 
    $dir = plugin_dir_url(__FILE__); 
    $JSON_config = file_get_contents($dir."config.json");


    $options_app_key = 
    get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "";

    $options_app_secret = 
    get_option('dbmenu_option_app_secret') != '' ? get_option('dbmenu_option_app_secret') : "";
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<header class="db-header">
    <div class="container">
        <div class="pull-left" id="designbold_nav_main_menu"></div>
        <div class="pull-right">
            <div class="menu-icon d-block d-lg-none">
                <i class="fal fa-bars"></i>
            </div>
            <div class="user-link d-none d-sm-block" id='designbold_login_nav'>
                <a href="javascript:" id="designbold_login" onclick="DBMN.designbold_login()" class="login"><span class="db-text">
                Login</span></a>
                <a href="javascript:" id="designbold_signup" class="signup"> <span class="db-text">Sign up</span></a>
            </div>
            <div id="designbold_user_info" class="user-info dropdown"></div>
        </div>
        <div class="clearfix"></div>
    </div>
    <nav class="menu-res" id="designbold_nav_menu_res"></nav>
</header>

<script type="text/template" id="designbold_nav_menu_res_tmpl">
    <div class="menu-res-inner">
        <ul>
            <% _.each(menu.menu_res, function(item){ %>
                <li><a href="<%= item.link %>" title="<%= item.title %>"><span class="db-text"><%= item.title %></span><%= item.icon %></a></li>
            <% }); %>
            <li id="designbold_res_login"><a href="javascript:" onclick="DBMN.designbold_login()" class="login"><span class="db-text">
                Login</span></a></li>
            <li id="designbold_res_signup"><a href="javascript:" onclick="DBMN.logout()" class="signup"> <span class="db-text">Sign up</span></a></li>
        </ul>
    </div>
</script>

<script type="text/template" id="designbold_nav_main_menu_tmpl">
    <a href="<%= menu.home %>" class="logo">
        <img alt="" src="<%= menu.logo %>">
    </a>
    <a href="<%= menu.home %>" class="logo-white">
        <img alt="" src="<%= menu.logo %>">
    </a>
    <nav class="main-menu d-none d-lg-block">
        <ul>
            <% _.each(menu.menu_item, function(item){ %>
                <li><a href="<%= item.link %>" title="<%= item.title %>"><span class="db-text"><%= item.title %></span><%= item.icon %></a></li>
            <% }); %>
        </ul>
    </nav>
</script>

<script type="text/template" id="db_user_nav_tmpl">
    <div class="user-avatar" data-toggle="dropdown">
        <span class="user-type">PRO</span>
        <img alt="<%= user.fullname %>" src="<%= user.avatar %>">
    </div>
    <div class="dropdown-menu user-info-dropdown">
        <div class="user-head">
            <div class="uh-avatar">
                <img alt="" src="<%= user.avatar %>">
            </div>
            <h3><%= user.fullname %><span class="upgrade">Upgrade</span></h3>
            <p><%= user.email %></p>
        </div>
        <div class="user-balance">
            <div class="balance">
                <div class="db-coins">
                    <div class="db-coins-icon">
                    </div>
                    <p><strong><%= user.budget %></strong> Coins</p>
                </div>
                <div class="db-coins db-bonus-coins">
                    <div class="db-coins-icon">
                    </div>
                    <p><strong><%= user.budget_bonus %></strong> Bonus Coins</p>
                </div>
            </div>
            <!-- <div class="user-stock">
                <p><span class="db-text">Monthly stocks</span> (620/1240)</p>
                <div class="db-stock-flex">
                    <div class="db-stock-progress">
                        <div class="stock-progress">
                            <div class="stock-progress-value" style="width: 60%"></div>
                        </div>
                    </div>
                    <label>620 <span class="db-text">lefts</span> </label>
                </div>
            </div> -->
        </div>
        <div class="line-under-stock"></div>
        <ul class="user-list-menu">
            <li>
                <a href="https://www.designbold.com/workspace/my-design" title="My designs" target="_blank">
                    <i class="fal fa-home"></i>
                    <span class="db-text"> My designs</span>
                </a>
            </li>
            <li>
                <a href="https://www.designbold.com/<%= user.fullname %>" title="Profile" target="_blank">
                    <i class="fal fa-user"></i>
                    <span class="db-text"> Profile</span>
                </a>
            </li>
            <li class="active">
                <a href="https://www.designbold.com/account/setting" title="Account settings" target="_blank"><i class="fal fa-cog"></i>
                    <span class="db-text">Account settings</span>
                </a>
            </li>
            <li>
                <a href="https://www.designbold.com/account/transaction" title="Transaction" target="_blank"> <i class="fal fa-usd-circle"></i>
                    <span class="db-text">Transaction</span>
                </a>
            </li>
            <li>
                <a href="https://www.designbold.com/account/invite-a-friend" title="Invite a friend" target="_blank">
                    <i class="fal fa-user-plus"></i>
                    <span class="db-text">Invite a friend</span>
                </a>
            </li>
            <li>
                <a href="javascript:" onclick="DBMN.logout()"><i class="fal fa-sign-out-alt"></i>
                    <span class="db-text"> Logout</span>
                </a>
            </li>
        </ul>
    </div>
</script>
<!-- End Designit header -->

<!-- Designit footer -->
<footer class="db-footer">
    <div class="container">
        <div class="footer-left">
            <div class="company">

                <a href="https://www.designbold.com/" class="logo">
                    <img alt="" src="<?= $dir ?>images/logo_white.svg">
                </a>
                <div class="social">
                    <a class="facebook" href="https://www.facebook.com/designbolddotcom/" target="_blank" rel="nofollow">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    <a class="twitter" href="https://twitter.com/DesignBold_" target="_blank" rel="nofollow">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a class="pinterest" href="https://www.pinterest.com/designbold/" target="_blank" rel="nofollow">
                        <i class="fab fa-pinterest"></i>
                    </a>
                    <a class="instagram" href="https://www.instagram.com/designbold/" target="_blank" rel="nofollow">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
                <p>DesignBold Academy provides critical knowledge on graphic design and printing, as well as essential complementary know-hows on marketing and technology that all need to prepare for Industry 4.0.
                Contact us:</p>
                <p>Email: academy@designbold.com</p>
            </div>


        </div>
        <div class="footer-right">
            <div class="footer-col">
                <h4><span class="db-value">COMPANY</span></h4>
                <ul class="sub_menu_footer">
                    <li><a href="https://www.designbold.com/about"> <span class="db-value">About Us</span></a></li>
                    <li><a href="https://www.designbold.com/team"><span class="db-value">Our Team</span></a></li>
                    <li><a href="https://www.designbold.com/features"><span class="db-value">Features</span></a></li>
                    <li><a href="https://www.designbold.com/pricing"><span class="db-value">Pricing</span></a></li>
                    <li><a href="https://www.designbold.com/term"><span class="db-value">Terms of Use</span></a></li>
                    <li><a href="https://www.designbold.com/policy"> <span class="db-value">Privacy Policy</span></a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><span class="db-value">PRODUCTS</span></h4>
                <ul class="sub_menu_footer">
                    <li><a href="https://developers.designbold.com/document/designit/"><span class="db-value">DesignIt Button</span></a></li>
                    <li><a href="https://www.designbold.com/brand-kit"><span class="db-value">Brand Kits</span></a></li>
                    <li><a href="https://www.designbold.com/templates"><span class="db-value">Templates</span></a></li>
                    <li><a href="https://www.designbold.com/resources"><span class="db-value">Resources</span></a></li>
                    <li><a href="https://www.designbold.com/contributor"><span class="db-value">Contributor</span></a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><span class="db-value">COMMUNITY &amp; SUPPORT</span></h4>
                <ul class="sub_menu_footer">
                    <li><a href="https://www.designbold.com/blog/" target="_blank"><span class="db-value">Academy</span></a></li>
                    <li><a href="https://www.designbold.com/support"><span class="db-value">FAQs &amp; Support</span></a></li>
                    <li><a href="https://www.designbold.com/contact"><span class="db-value">Contact Us</span></a></li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="allright">
            <p><span class="db-value">© 2018 - DesignBold Academy - Graphic Design and Printing Knowledge. All Rights Reserved.</span> </p>
        </div>
    </div>
</footer>
<!-- End Designit footer -->
<script type="text/javascript" src="<?= $dir ?>js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js"></script>

<script type="text/javascript">
    var designbold_json_config = {};

    designbold_json_config = <?= $JSON_config ?>;

    var designbold_nav_main_menu_template = _.template($('#designbold_nav_main_menu_tmpl').html());
    var designbold_nav_menu_res_template = _.template($('#designbold_nav_menu_res_tmpl').html());

    $('#designbold_nav_main_menu').html(designbold_nav_main_menu_template({
        menu : designbold_json_config.header,
    }));

    $('#designbold_nav_menu_res').html(designbold_nav_menu_res_template({
        menu : designbold_json_config.header,
    }));
</script>

<script type="text/javascript">
    var DBMN = DBMN || {};

    DBMN.app = {
        'app_key' : '<?= $options_app_key ?>',
        'app_secret' : '<?= $options_app_secret ?>',
        'redirect_url' : "<?= plugins_url('/designitmenu/designbold.php') ?>",
        'internal_debug' : false,
        'scope' : '*.*',
    }
</script>
