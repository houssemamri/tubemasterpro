<aside class="left-side sidebar-offcanvas" style="min-height: 400px;">
                <!-- sidebar: style can be found in sidebar.less -->
<section class="sidebar">
    
    <ul class="sidebar-menu">
                    
        <li <?php if ( $active == 'home' ) { echo 'class="active"'; } ?>>
            <a href="<?php echo base_url(); ?>"><img title="Home" class="sidebar-icons" src="<?php echo assets_url('images/home-01.svg'); ?>" style="padding-bottom: 7px;padding-right: 5px;" /><span>Home</span></a>
        </li>   
       
        <li class="treeview <?php echo ( $parent == 'target' ) ? 'active' : 'active'; ?>">
            
            <a href="#">
                <img title="Target" class="sidebar-icons" src="<?php echo assets_url('images/target-01.svg'); ?>" />
                <span>Target</span>
            </a>
            
            <ul class="treeview-menu" style="display:block;">
                
                <li <?php if ( $active == 'keyword_search' ) { echo 'class="active"'; } ?>>
                    <a href="<?php echo base_url(); ?>dashboard/keyword_search/" style="margin-left: 10px;">
                        <img title="Keyword Search" class="sidebar-icons" src="<?php echo assets_url('images/keyword-search-01.svg'); ?>" />
                        Keyword Search
                    </a>
                </li>
                <li <?php if ( $active == 'video_search' ) { echo 'class="active"'; } ?>>
                    <a href="<?php echo base_url(); ?>dashboard/video_search/" style="margin-left: 10px;">
                        <img title="Video Search" class="sidebar-icons" src="<?php echo assets_url('images/video-search-01.svg'); ?>" />
                        Video Search
                    </a>
                </li>
                <li <?php if ( $active == 'channel_search' ) { echo 'class="active"'; } ?>>
                    <a href="<?php echo base_url(); ?>dashboard/channel_search/" style="margin-left: 10px;">
                        <img title="Channel Search" class="sidebar-icons" src="<?php echo assets_url('images/channel-search-01.svg'); ?>" />
                        Channel Search
                    </a>
                </li>
                <li id="hook-eight" <?php if ( $active == 'target_list' ) { echo 'class="active"'; } ?>>
                    <a href="<?php echo base_url(); ?>dashboard/target_list/" style="margin-left: 10px;">
                        <img title="Target Lists" class="sidebar-icons" src="<?php echo assets_url('images/target-lists-01.svg'); ?>" />
                        Target List
                    </a>
                </li>
           
            </ul>
            
        </li>
        
        <li id="hook-eleven" <?php if ( $active == 'adwords_export' ) { echo 'class="active"'; } ?>>
            <a href="<?php echo base_url(); ?>dashboard/adwords_export/">
                <img title="Campaign Builder" class="sidebar-icons" src="<?php echo assets_url('images/campaign-builder-01.svg'); ?>" />
                <span>Campaign Builder</span>
            </a>
        </li>

        <li class="treeview <?php echo ( $parent == 'optimizer' ) ? 'active' : 'active'; ?>">
            
            <a href="#">
                <img title="Optimizer" class="sidebar-icons" src="<?php echo assets_url('images/campaign-optimizer.svg'); ?>" />
                <span>Optimizer</span>
            </a>

            <ul class="treeview-menu" style="display:block;">
                
                <li <?php if ( $data['o']['active_sub'] == 'new' ) { echo 'class="active"'; } ?>>
                    <a href="<?php echo base_url(); ?>overview/main">
                        <img title="Create New Optimized Campaigns" class="sidebar-icons" src="<?php echo assets_url('images/new-campaign-optimizer.svg'); ?>" />
                        <span>Create New Optimized Campaign</span>
                    </a>
                </li>

                <li <?php if ( $data['o']['active_sub'] == 'existing' ) { echo 'class="active"'; } ?>>
                    <a href="<?php echo base_url(); ?>overview/main/overview/existing">
                        <img title="Existing Optimized Campaigns" class="sidebar-icons" src="<?php echo assets_url('images/update-campaign-optimizer.svg'); ?>" />
                        <span>Existing Optimized Campaigns</span>
                    </a>
                </li>

            </ul>

        </li>
        <!--
<li <?php if ( $active == 'campaign_optimizer' ) { echo 'class="active"'; } ?>>
            <a href="<?php echo base_url(); ?>dashboard/campaign_optimizer/">
                <img title="Campaign Optimizer" class="sidebar-icons" src="<?php echo assets_url('images/campaign-builder-01.svg'); ?>" />
                <span>Campaign Optimizer</span>
            </a>
        </li>
-->

        <li class="treeview <?php if ( $parent == 'tube_university' ) echo 'active'; ?>">
            <a href="<?php echo base_url(); ?>dashboard/tube_university/">
                <img title="Tube University" class="sidebar-icons" src="<?php echo assets_url('images/tube-university-01.svg'); ?>" />
                <span>Tube University <?php if ($this->ion_auth->in_group(3) ){?> <i class="fa fa-lock fa-3" style="color:#843534; font-size: 25px;"></i><?php }?></span>
            </a>
            
        </li>
        <li class="treeview" <?php if ( $active == 'video_ad_production' ) { echo 'class="active"'; } ?>>
                    <a href="#"  id="vid_ad_prod">
                        <img title="Video Ad Production" class="sidebar-icons" src="<?php echo assets_url('images/video ad production-01.svg'); ?>" />
                        Video Ad Production
                    </a>
        </li>
       
        <!--
        <li class="treeview ">
            
            <a href="#" id="moveLearn">
                <i class="fa fa-flag"></i>
                <span>Learn</span>
            </a>
                        <ul class="treeview-menu">
                
                                
                               
            </ul>
           
        </li>
        -->
       
        <li class="hidden-lg hidden-md hidden-sm">
             <a href="#">My Account</a>
        </li>
        <li class="hidden-lg hidden-md hidden-sm">
             <a href="#">Sign out</a>
        </li>
               
    </ul>
    
    <div class="justbox hide">
        <h4>Lorem Ipsum</h4>
        <p>Lorem Ipsum</p>
    </div>
</section>
<!-- /.sidebar -->
</aside>