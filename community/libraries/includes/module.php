<?php
//    $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype', 'scriptaculous/scriptaculous'));
    $className = $_GET['op'];
    if (isset($loadedModules[$className])) {

        // Get top title navigational links
        $nav_links = $loadedModules[$className] -> getNavigationLinks();
        if ($nav_links) {
            $links_size = sizeof($nav_links);
            $count = 0;
            $module_nav_links = "";
            foreach ($nav_links as $link) {
                if (isset($link['onclick'])) {
     $module_nav_links .= '<a class="titleLink" href ="javascript:void(0)" onclick="'.$link['onclick'].'">'.$link['title'].'</a>';
    }else {
     $module_nav_links .= '<a class="titleLink" href ="'.$link['link'].'">'.$link['title'].'</a>';
    }
                $count++;
                if ($count < $links_size) {
                    $module_nav_links .= '&nbsp;&raquo;&nbsp;';
                }
            }
        } else {
            $module_nav_links = '<a class="titleLink" href ="'.$loadedModules[$className] -> moduleBaseUrl.'">'.$loadedModules[$className] -> getName().'</a>';
        }
        $smarty -> assign("T_MODULE_NAVIGATIONAL_LINKS", $module_nav_links);

        // Get link to highlight on the left sidebar
        $highlight = $loadedModules[$className] -> getLinkToHighlight();
        if ($highlight) {
            $highlight = $loadedModules[$className] -> className . "_" . $highlight;
        } else {
            $highlight = $loadedModules[$className] -> className;
        }
        $smarty -> assign("T_MODULE_HIGHLIGHT", $highlight);

        // Get module html - two ways: pure HTML or PHP+smarty
        // If no smarty file is defined then false will be returned
        if ($module_smarty_file = $loadedModules[$className] -> getSmartyTpl()) {
            // Execute the php code
            $loadedModules[$className] -> getModule();
            // Let smarty know to include the module smarty file
            $smarty -> assign("T_MODULE_SMARTY", $module_smarty_file);
        } else {
            // Present the pure HTML code
            $smarty -> assign("T_MODULE_PAGE", $loadedModules[$className] -> getModule());
        }
    } else {
        $message = _ERRORLOADINGMODULE;
        $message_type = "failure";
    }
