<header class="db-header">
    <div class="container">
        <div class="pull-left" id="designbold_nav_main_menu">
            <?php if($config->header->logo !== '') : ?>
                <a href="<%= $config->header->home %>" class="logo">
                    <img alt="" src="<%= $config->header->logo %>">
                </a>
                <a href="<%= $config->header->home %>" class="logo-white">
                    <img alt="" src="<%= $config->header->logo %>">
                </a>
            <?php endif; ?>
            <nav class="main-menu d-none d-lg-block">
                <?php 
                wp_nav_menu(array(
                    'theme_location' => get_option('dbmenu_option_menu_name'),
                    'container'       => '',
                    'container_class' => '',
                    'echo'            => true,
                    'fallback_cb'     => 'wp_page_menu',
                    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth'           => 0
                ));?>
            </nav>
        </div>
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
    <nav class="menu-res" id="designbold_nav_menu_res">
        <div class="menu-res-inner">
            <?php wp_nav_menu(array(
                'theme_location' => get_option('dbmenu_option_menu_name'),
                'container'       => '',
                'container_class' => '',
                'echo'            => true,
                'fallback_cb'     => 'wp_page_menu',
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'depth'           => 0
            )); ?>
        </div>
    </nav>
</header>

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
            <h3><%= user.fullname %>
                <% if ([3,8].indexOf(user.group_id) != -1){ %>
                <span class="upgrade">Upgrade</span>
                <% } %>
            </h3>
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