<?php
/**
 * Created by PhpStorm.
 * User: lubchik
 * Date: 6/2/2017
 * Time: 2:24 AM
 */
include_once dirname(dirname(__FILE__)) .'/Logic/DynamicInternal.php';
include_once dirname(dirname(__FILE__)) .'/Logic/DynamicGeneral.php';

class DynamicHelper
{
    public function Install($networkwide)
    {
        //TODO: initiate database

        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            if ($networkwide) {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->ActivateThisBlog();
                }
                switch_to_blog($old_blog);
                return;


            }

        }
        else
        {
            $this->ActivateThisBlog();
        }

    }

    public function ActivateThisBlog()
    {
        $this->UpdateThisBlog();
    }

    public function UpdateThisBlog()
    {
        //some special action to do with this blog
    }

    public function Remove()
    {
        //nothing right now
    }

    public function ManageRequest($request)
    {
        //TODO: validate version of code
        $DynamicInternal=new DynamicInternal();
        echo $DynamicInternal->Manage($request);
        die();
    }

    public function RegisterScripts($dirUrl)
    {
        //TODO: validate version of code
        $dir =  dirname(dirname(__FILE__)) ;
        $DynamicGeneral=new DynamicGeneral();
        $DynamicCMS = new DynamicCMS();

        $files = array();
        array_push($files,array('path'=>"/files/cssDependencies.txt","type"=>'style', 'register'=>'wp_register_style','deregister'=>'wp_deregister_style','enque'=>'wp_enqueue_style', 'filename'=>'css','addtopath'=>'css/'));
        array_push($files,array('path'=>"/files/jqueryDependencies.txt","type"=>'script', 'register'=>'wp_register_script','deregister'=>'wp_deregister_script','enque'=>'wp_enqueue_script', 'filename'=>'script', 'addtopath'=>'jscripts/'));

        foreach($files as $file)
        {
            $myDependencies = $DynamicGeneral->getData($dir.$file['path']);
            foreach($myDependencies as $dependency)
            {
                $key = $dependency['url'];
                $pos = strrpos($_SERVER["REQUEST_URI"],$key);
                $pos6= strrpos($_SERVER["REQUEST_URI"],"wp-admin");

                $dynamicMenu = $DynamicCMS->checkPosition($_SERVER["REQUEST_URI"],"dynamic_data_menu");
                $jname='jqury'.$file['type'].'_'.$dependency['url']."_".$dependency['name'];
                if($pos || (!$pos6 && $dependency['url']=="frontPage") || ($pos6 && $dependency['url']=="wpadmin") ||($dependency['url']=="frontPage" && $dynamicMenu ) )
                {
                    $fileurl = $dependency[$file['filename']];
                    if($dependency['type']=='internal')
                    {
                        $fileurl = $dirUrl.$file['addtopath'].$dependency[$file['filename']];
                    }

                    call_user_func($file['deregister'], $jname);
                    call_user_func($file['register'], $jname,$fileurl);
                    call_user_func($file['enque'], $jname);
                }
            }
        }



    }

    public function AddAdminMenues()
    {
        //TODO: validate version of code
        //TODO: load menus from table
        $DynamicCMS= new DynamicCMS();
        $userRole = strtolower($DynamicCMS->get_current_user_role());

        if($userRole=="administrator") // you can add more parameters
        {
            $dir =  dirname(dirname(__FILE__)) ;

            $DynamicGeneral=new DynamicGeneral();
            //initiate your menues in adminmenus
            $myMenus = $DynamicGeneral->getData($dir."/files/adminmenus.txt");

            foreach($myMenus as $menu)
            {
                switch($menu['type'])
                {
                    case "menu":
                        add_menu_page($menu['name'], $DynamicCMS->Translate($menu['name']), 'manage_options', $menu['uniqueid'], create_function('','DynamicCreateDesign("'.$menu['formname'].'");'));
                        break;
                    case "submenu":
                        add_submenu_page($menu['parentuniqueid'],$menu['name'], $DynamicCMS->Translate($menu['name']), 'manage_options', $menu['uniqueid'], create_function('','DynamicCreateDesign("'.$menu['formname'].'");'));

                        break;
                    case "subpage":
                        add_submenu_page(null,$menu['name'], $DynamicCMS->Translate($menu['name']), 'manage_options', $menu['uniqueid'], create_function('','DynamicCreateDesign("'.$menu['formname'].'");'));
                        break;
                }
            }
        }

        function DynamicCreateDesign($formName)
        {
           //something creates here menu design
        }


    }

    public function ContentFilter($content)
    {
       //Do something with content
        return $content;
    }

    public function GetShortcode($atts)
    {
        $attributes = shortcode_atts( array(
            'attr1' => '',
            'attr2'=>''
        ), $atts );

        $shortag="";
        if(isset($attributes['attr1']) && trim($attributes['attr2'])!=null)
        {
            //do something with attributes
            //$shortag
        }
        // return shortag result;
        return $shortag;
    }
}