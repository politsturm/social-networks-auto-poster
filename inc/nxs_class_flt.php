<?php

class nxs_Filters
{
    public static $posts;
    public static $posts_types;
    public static $post_formats;
    public static $taxonomies;
    public $filterText;

    public function __construct()
    {
        //add_action( 'init',                  array( __CLASS__, 'create_filter' ) );
        // add_action( 'save_post',             array( __CLASS__, 'save_filter' ) );
        add_action('admin_head', array( __CLASS__, 'init' ));
        add_action('admin_enqueue_scripts', array( __CLASS__, 'enqueue' ));
    }

    public static function init($od=false)
    {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
        } else {
            $od=true;
        }//prr($screen->id );// prr($screen); var_dump($od);        toplevel_page_NextScripts_SNAP-network
        if ($od || (!empty($screen) && (stripos($screen->id, 'NextScripts_SNAP')!==false || stripos($screen->id, 'nxssnap')!==false || stripos($screen->id, '_page_nxs')!==false))) {
            $builtin_types     = get_post_types(array( 'public' => true, '_builtin' => true ));
            $custom_types      = get_post_types(array( 'public' => true, '_builtin' => false ));
            self::$posts_types = array_merge($builtin_types, $custom_types);
            self::$posts_types[] = 'nxs_qp';
            self::$posts_types[] = 'BuddyPress_Activity';
            natsort(self::$posts_types);

            $builtin_taxonomies       = get_taxonomies(array( 'public' => true, '_builtin' => true ));
            $custom_taxonomies        = get_taxonomies(array( 'public' => true, '_builtin' => false ));
            $builtin_taxonomies2       = get_taxonomies(array( 'public' => false, '_builtin' => true ));
            $custom_taxonomies2        = get_taxonomies(array( 'public' => false, '_builtin' => false ));
            self::$taxonomies         = array_merge($builtin_taxonomies, $custom_taxonomies, $builtin_taxonomies2, $custom_taxonomies2);
            natsort(self::$taxonomies);

            // self::$posts = get_posts( array( 'post_type' => self::$posts_types, 'numberposts' => -1, 'post_status' => 'any' ) ); prr(self::$posts); prr(self::$posts_types);  WTF?????? Retreiving all posts in init?????
            self::$post_formats = get_theme_support('post-formats'); // prr(self::$post_formats);
        }
    }

    public static function showEdit($pg)
    {
        add_meta_box('nxs_title_metabox', __('Title and Options', 'social-networks-auto-poster-facebook-twitter-g'), array( __CLASS__, 'print_title_metabox' ), $pg, 'normal', 'high');
        add_meta_box('nxs_schedule_metabox', __('When to post (Schedule)', 'social-networks-auto-poster-facebook-twitter-g'), array( __CLASS__, 'print_schedule_metabox' ), $pg, 'normal', 'high');
        add_meta_box('nxs_network_metabox', __('Where to post (Network Selection)', 'social-networks-auto-poster-facebook-twitter-g'), array( __CLASS__, 'print_networks_metabox' ), $pg, 'normal', 'high');
        add_meta_box('nxs_posts_metabox', __('What to post (Posts, Pages, etc.. selection)', 'social-networks-auto-poster-facebook-twitter-g'), array( __CLASS__, 'print_posts_metabox' ), $pg, 'normal', 'high');
    }

    public static function enqueue()
    {
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
        } else {
            return;
        }  // prr($screen->id);
        if (!empty($screen) && is_object($screen) && ($screen->id == 'nxs_filter' || stripos($screen->id, 'NextScripts_SNAP')!==false || stripos($screen->id, 'nxssnap')!==false || stripos($screen->id, '_page_nxs')!==false)) {
            //## Push some data to JS //?? Check why we need it
            $data = array(
                'list_select_ids' => array(
                    'nxs_name_post',
                    'nxs_name_page',
                    'nxs_name_parent',
                    'nxs_post_ids',
                    'nxs_post_status',
                    'nxs_post_type',
                    'nxs_post_formats',
                    'nxs_tags_names',
                    'nxs_cats_names',
                    'nxs_user_names',
                    'nxs_langs',
                    'nxs_pagination',
                    'nxs_sticky_post',
                    'nxs_order',
                    'nxs_order_by',
                    'nxs_meta_value',
                    'nxs_meta_key',
                    'nxs_meta_operator',
                    'nxs_meta_type',
                    'nxs_permission',
                    'nxs_cache_results',
                    'nxs_cache_meta',
                    'nxs_cache_term',
                    'nxs_meta_relation',
                    'nxs_term_names',
                    'nxs_tax_names',
                    'nxs_term_operator',
                    'nxs_term_relation',
                    'nxs_term_children',
                    'nxs_types_starting_abs_period',
                    'nxs_types_end_abs_period' ),

                'list_input_ids' => array(
                    'nxs_starting_period',
                    'nxs_end_period'
                )
            );
            wp_localize_script('jquery', 'nxs', $data);
        }
    }

    public static function create_filter()
    {
        $labels = array(
            'name'               => __('Filters', 'social-networks-auto-poster-facebook-twitter-g'),
            'singular_name'      => __('Filter', 'social-networks-auto-poster-facebook-twitter-g'),
            'add_new'            => __('Add Filter', 'social-networks-auto-poster-facebook-twitter-g'),
            'add_new_item'       => __('Add new Filter', 'social-networks-auto-poster-facebook-twitter-g'),
            'edit_item'          => __('Edit Filter', 'social-networks-auto-poster-facebook-twitter-g'),
            'new_item'           => __('New Filter', 'social-networks-auto-poster-facebook-twitter-g'),
            'view_item'          => __('View Filter', 'social-networks-auto-poster-facebook-twitter-g'),
            'search_items'       => __('Find Filters', 'social-networks-auto-poster-facebook-twitter-g'),
            'not_found'          => __('Filter not found', 'social-networks-auto-poster-facebook-twitter-g'),
            'not_found_in_trash' => __('Filter not found in trash', 'social-networks-auto-poster-facebook-twitter-g'),
            'menu_name'          => __('Filters', 'social-networks-auto-poster-facebook-twitter-g')
        );

        $args = array(
            'labels'    => $labels,
            'show_ui'   => false,
            'menu_icon' => 'dashicons-forms',
            'supports'  => array( 'title' ),
            'show_in_menu' => 'admin.php?page=nxssnap-reposter2',
            'capabilities' => array(
          //       'create_posts' => false, // Removes support for the "Add New" function
            )

        );

        register_post_type('nxs_filter', $args);
    }

    public static function save_filter($post_id)
    {
        if (!isset($_POST['nxs_metabox_nonce']) || !wp_verify_nonce($_POST['nxs_metabox_nonce'], basename(__FILE__))) {
            return $post_id;
        }
        $pvData = self::sanitize_data($_POST);
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        if (get_post_type($post_id) === 'nxs_filter') {
            //## Add New Cats and Tags
            if (!empty($pvData['nxs_cats_names'])) {
                $toIns = array();
                $nCats = array();
                $cats = get_categories(array('hide_empty'=>false));
                if ($cats) {
                    foreach ($cats as $cat) {
                        $nCats[] = $cat->term_id;
                    }
                }
                foreach ($pvData['nxs_cats_names'] as $ctp) { //prr($ctp);
                    if (!in_array($ctp, $nCats)) {
                        $ctp = wp_insert_term($ctp, 'category');
                        if (!is_nxs_error($ctp)) {
                            $ctp = $ctp['term_id'];
                        } else {
                            continue;
                        }
                    }
                    $toIns[] = $ctp;
                }
                $pvData['nxs_cats_names'] = $toIns;// prr($toIns);
            }
            if (!empty($pvData['nxs_tags_names'])) {
                $nTags = array();
                $toIns = array();
                $tags = get_tags(array( 'hide_empty' => false ));
                if ($tags) {
                    foreach ($tags as $tag) {
                        $nTags[] = $tag->term_id;
                    }
                }
                foreach ($pvData['nxs_tags_names'] as $ctp) { // prr($ctp);
                    if (!in_array($ctp, $nTags)) {
                        $ctp = wp_insert_term($ctp, 'post_tag');
                        if (!is_nxs_error($ctp)) {
                            $ctp = $ctp['term_id'];
                        } else {
                            continue;
                        }
                    }
                    $toIns[] = $ctp;
                }
                $pvData['nxs_tags_names'] = $toIns; // prr($toIns);
            }

            $count_compares          = !empty($pvData['nxs_count_meta_compares'])?intval($pvData['nxs_count_meta_compares']):0;
            $count_term_compares     = !empty($pvData['nxs_count_term_compares'])?intval($pvData['nxs_count_term_compares']):0;
            $count_date_periods      = !empty($pvData['nxs_count_date_periods'])?intval($pvData['nxs_count_date_periods']):0;
            $count_date_abs_periods  = !empty($pvData['nxs_count_abs_periods'])?intval($pvData['nxs_count_abs_periods']):0;

            $settings = array( 'name_post', 'name_page', 'name_parent', 'post_status', 'post_type', 'post_formats', 'ie_tags_names', 'ie_cats_names', 'tags_names', 'cats_names', 'user_names', 'langs', 'post_ids', 'search_keywords', 'pagination', 'post_per_page', 'sticky_post', 'paged', 'post_per_archive_page', 'offset', 'order', 'order_by', 'year', 'month', 'day', 'hour', 'minute', 'second', 'permission', 'cache_results', 'cache_meta', 'cache_term', 'count_compares', 'term_children', 'term_operator', 'term_names', 'tax_names', 'term_relation', 'count_term_compares', 'starting_period', 'end_period', 'inclusive', 'count_date_periods', 'count_abs_periods', 'starting_abs_period', 'end_abs_period', 'types_starting_abs_period', 'types_end_abs_period', 'NPNts' );

            $setToSave = array();
            $pval = $pvData;

            //## Unset supplemental fields.
            if (isset($pvData['nxs_term_children']) && empty($pvData['nxs_term_names'])) {
                unset($pvData['nxs_term_children']);
            }
            if (isset($pvData['nxs_term_operator']) && empty($pvData['nxs_term_names'])) {
                unset($pvData['nxs_term_operator']);
            }
            if (isset($pvData['nxs_term_relation']) && empty($pvData['nxs_term_names'])) {
                unset($pvData['nxs_term_relation']);
            }
            // if (isset($pvData['nxs_count_term_compares']) && empty($pvData['nxs_term_names'])) unset($pvData['nxs_count_term_compares']);

            //## Post_Meta
            if (!empty($pval['nxs_count_meta_compares'])) {
                $setToSave['nxs_count_meta_compares'] = $pval['nxs_count_meta_compares'];
            }
            if (!empty($pval['nxs_meta_key']) && $pval['nxs_meta_key']!='snap_isAutoPosted' && $pval['nxs_meta_key']!='snap_isRpstd'.$post_id) {
                $setToSave['post_meta'][0]['operator'] = (isset($pval['nxs_meta_operator']))?$pval['nxs_meta_operator']:'';
                $setToSave['post_meta'][0]['key'] = (isset($pval['nxs_meta_key']))?$pval['nxs_meta_key']:'';
                $setToSave['post_meta'][0]['value'] = (isset($pval['nxs_meta_value']))?$pval['nxs_meta_value']:'';
                $setToSave['post_meta'][0]['relation'] = (isset($pval['nxs_meta_relation']))?$pval['nxs_meta_relation']:'';
            }
            $jjj = 1;
            if (!empty($pval['nxs_count_meta_compares']) && (int)$pval['nxs_count_meta_compares']>1) {
                for ($jj = 2; $jj <= $pval['nxs_count_meta_compares']; $jj++) {
                    if (!empty($pval['nxs_meta_key_'.$jj]) && $pval['nxs_meta_key_'.$jj]!='snap_isAutoPosted' && $pval['nxs_meta_key']!='snap_isRpstd'.$post_id) {
                        $pm = array();
                        $jjj++;
                        $pm['operator'] = (isset($pval['nxs_meta_operator_'.$jj]))?$pval['nxs_meta_operator_'.$jj]:'';
                        $pm['key'] = (isset($pval['nxs_meta_key_'.$jj]))?$pval['nxs_meta_key_'.$jj]:'';
                        $pm['value'] = (isset($pval['nxs_meta_value_'.$jj]))?$pval['nxs_meta_value_'.$jj]:'';
                        $pm['relation'] = (isset($pval['nxs_meta_relation_'.$jj]))?$pval['nxs_meta_relation_'.$jj]:'';
                        $setToSave['post_meta'][] = $pm;
                    }
                }
            }
            if (isset($pvData['nxs_rpstr']['rpstOnlyPUP'])) {
                $pm = array();
                $jjj++;
                $pm['operator'] = 'NOT EXISTS';
                $pm['key'] = 'snap_isAutoPosted';
                $pm['value'][] = '';
                $pm['relation'] = 'AND';
                $setToSave['post_meta'][] = $pm;
            }
            if (isset($pvData['nxs_rpstr']['rpstOnlyUniq']) || (isset($pvData['nxs_rpstr']['rpstType']) && $pvData['nxs_rpstr']['rpstType']!='1')) {
                $pm = array();
                $jjj++;
                $pm['operator'] = 'NOT EXISTS';
                $pm['key'] = 'snap_isRpstd'.$post_id;
                $pm['value'][] = '';
                $pm['relation'] = 'AND';
                $setToSave['post_meta'][] = $pm;
            }

            $setToSave['nxs_count_meta_compares'] = !empty($setToSave['post_meta'])?count($setToSave['post_meta']):0;

            if (isset($pvData['nxs_count_date_periods']) && empty($pvData['nxs_end_period']) && empty($pvData['nxs_starting_period'])) {
                unset($pvData['nxs_count_date_periods']);
            }
            if (isset($pvData['nxs_inclusive']) && empty($pvData['nxs_end_period']) && empty($pvData['nxs_starting_period'])) {
                unset($pvData['nxs_inclusive']);
            }

            //## If Taxonomy name is set and value is not - remove it
            if (isset($pvData['nxs_tax_names']) && empty($pvData['nxs_term_names'])) {
                unset($pvData['nxs_tax_names']);
            }

            if (!empty($pvData['nxs_term_names']) && !empty($pvData['nxs_tax_names']) && is_array($pvData['nxs_term_names'])) {
                $outT = array();
                foreach ($pvData['nxs_term_names'] as $g) {
                    $term = get_term($g, $pvData['nxs_tax_names']);
                    if (!is_object($term)) {
                        $t = wp_insert_term($g, $pvData['nxs_tax_names']);
                        $outT[] = $t['term_id'];
                    } else {
                        $outT[] = $g;
                    }
                }
                $pvData['nxs_term_names'] = $outT;
            }

            if ($count_term_compares > 1) {
                for ($j = 2; $j <= $count_term_compares; $j++) {
                    $settings[] = 'term_children_' .$j;
                    $settings[] = 'term_operator_' .$j;
                    $settings[] = 'term_names_' .$j;
                    $settings[] = 'tax_names_' .$j;

                    if (!empty($pvData['nxs_term_names_' .$j]) && !empty($pvData['nxs_tax_names_' .$j]) && is_array($pvData['nxs_term_names_' .$j])) {
                        $outT = array();
                        foreach ($pvData['nxs_term_names_' .$j] as $g) {
                            $term = get_term($g, $pvData['nxs_tax_names_' .$j]);
                            if (!is_object($term)) {
                                $t = wp_insert_term($g, $pvData['nxs_tax_names_' .$j]);
                                $outT[] = $t['term_id'];
                            } else {
                                $outT[] = $g;
                            }
                        }
                        $pvData['nxs_term_names_' .$j] = $outT;
                    }
                }
            }

            if ($count_date_periods > 1) {
                for ($k = 2; $k <= $count_date_periods; $k++) {
                    $settings[] = 'starting_period_' .$k;
                    $settings[] = 'end_period_' .$k;
                    $settings[] = 'inclusive_' .$k;
                }
            }

            if (!empty($pvData['nxs_start_abs_period']) || !empty($pvData['nxs_end_abs_period'])) {
                $setToSave['absPeriods'][0]['start'] = (isset($pvData['nxs_start_abs_period']))?$pvData['nxs_start_abs_period']:'';
                $setToSave['absPeriods'][0]['typeStart'] = (isset($pvData['nxs_start_abs_period_type']))?$pvData['nxs_start_abs_period_type']:'';
                $setToSave['absPeriods'][0]['end'] = (isset($pvData['nxs_end_abs_period']))?$pvData['nxs_end_abs_period']:'';
                $setToSave['absPeriods'][0]['typeEnd'] = (isset($pvData['nxs_end_abs_period_type']))?$pvData['nxs_end_abs_period_type']:'';
            }
            $jjj = 1;
            if ($count_date_abs_periods > 1) {
                for ($jj = 2; $jj <= $count_date_abs_periods; $jj++) {
                    if (!empty($pvData['nxs_start_abs_period_'.$jj]) || !empty($pvData['nxs_end_abs_period_'.$jj])) {
                        $pm = array();
                        $jjj++;
                        $pm['start'] = (isset($pvData['nxs_start_abs_period_'.$jj]))?$pvData['nxs_start_abs_period_'.$jj]:'';
                        $pm['typeStart'] = (isset($pvData['nxs_start_abs_period_type_'.$jj]))?$pvData['nxs_start_abs_period_type_'.$jj]:'';
                        $pm['end'] = (isset($pvData['nxs_end_abs_period_'.$jj]))?$pvData['nxs_end_abs_period_'.$jj]:'';
                        $pm['typeEnd'] = (isset($pvData['nxs_end_abs_period_type_'.$jj]))?$pvData['nxs_end_abs_period_type_'.$jj]:'';
                        $setToSave['absPeriods'][] = $pm;
                    }
                }
            }
            //prr($setToSave);
            //##
            $ntOpts = array();
            if (!empty($pvData['nxs_NPNts'])) {
                foreach ($pvData['nxs_NPNts'] as $ntC) {
                    $ntA = explode('--', $ntC);
                    $ntOpts[$ntA[0]][$ntA[1]] = 1;
                }
            }
            $pvData['nxs_NPNts'] = $ntOpts; // prr($ntOpts);
            //## Save reposter
            foreach ($settings as $setting) {
                $full_setting_name = 'nxs_'. $setting;
                if (!empty($pvData[$full_setting_name])) {
                    $setToSave[$full_setting_name] = $pvData[$full_setting_name];
                }
            }
            //prr($setToSave, 'SAVED FILTERS/REPOSTER SETTINGS');
            self::save_meta($post_id, 'nxs_rpstr_data', $setToSave); //   echo "-= 1 =-";  prr($setToSave);
        }
        return $setToSave;
    }

    public static function save_schinfo($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
        $optionsii = maybe_unserialize(get_post_meta($post_id, 'nxs_rpstr', true));
        $pval = $_POST['nxs_rpstr'];
        if (empty($optionsii)) {
            $optionsii = array();
        }

        if (empty($optionsii['rpstDays'])) {
            $optionsii['rpstDays'] = 0;
        }
        if (empty($optionsii['rpstHrs'])) {
            $optionsii['rpstHrs'] = 0;
        }
        if (empty($optionsii['rpstMins'])) {
            $optionsii['rpstMins'] = 0;
        }
        $rpstEvrySecEx = $optionsii['rpstDays']*86400+$optionsii['rpstHrs']*3600+$optionsii['rpstMins']*60;
        $isRpstWasOn = isset($optionsii['rpstOn']) && $optionsii['rpstOn']=='1';

        if (isset($pval['rpstOn'])) {
            $optionsii['rpstOn'] = $pval['rpstOn'];
        } else {
            $optionsii['rpstOn'] = 0;
        }

        if (isset($pval['rpstDays'])) {
            $optionsii['rpstDays'] = trim($pval['rpstDays']);
        }
        if (isset($pval['rpstHrs'])) {
            $optionsii['rpstHrs'] = trim($pval['rpstHrs']);
        }
        if ((int)$optionsii['rpstHrs']>23) {
            $optionsii['rpstHrs'] = 23;
        }
        if (isset($pval['rpstMins'])) {
            $optionsii['rpstMins'] = trim($pval['rpstMins']);
        }
        if ((int)$optionsii['rpstMins']>59) {
            $optionsii['rpstMins'] = 59;
        }
        if (isset($pval['rpstRndMins'])) {
            $optionsii['rpstRndMins'] = trim($pval['rpstRndMins']);
        }

        if ($optionsii['rpstRndMins']>30) {
            $optionsii['rpstRndMins'] = '30';
        }
        if (empty($optionsii['rpstDays']) && empty($optionsii['rpstHrs']) && $optionsii['rpstRndMins']>($optionsii['rpstMins']/2)) {
            $optionsii['rpstRndMins'] = ceil(($optionsii['rpstMins']/2)-1);
        }

        if (isset($pval['rpstPostIncl'])) {
            $optionsii['rpstPostIncl'] = trim($pval['rpstPostIncl']);
        }

        if (isset($pval['rpstStop'])) {
            $optionsii['rpstStop'] = trim($pval['rpstStop']);
        } else {
            $optionsii['rpstStop'] = 'O';
        }

        $rpstEvrySecNew = $optionsii['rpstDays']*86400+$optionsii['rpstHrs']*3600+$optionsii['rpstMins']*60;
        $rpstRNDSecs = isset($optionsii['rpstRndMins'])?$optionsii['rpstRndMins']*60:0;
        if ($rpstRNDSecs>$rpstEvrySecNew) {
            $optionsii['rpstRndMins'] = 0;
        }

        global $nxs_cTime;
        $nxs_cTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
        if (isset($pval['rpstCustTD']) && is_array($pval['rpstCustTD'])) {
            $pval['rpstCustTD'] = array_filter($pval['rpstCustTD'], create_function('$value', 'global $nxs_cTime; return !empty($value) && strtotime($value)>$nxs_cTime;'));
            sort($pval['rpstCustTD']);
            $optionsii['rpstCustTD'] = $pval['rpstCustTD'];
        } else {
            $optionsii['rpstCustTD'] = array();
        }

        $isTD = (isset($pval['rpstTimes']) && $pval['rpstTimes']=='S' && !empty($pval['rpstCustTD']) && !empty($pval['rpstCustTD'][0]));
        if (empty($optionsii['rpstNxTime'])) {
            $optionsii['rpstNxTime'] = 0;
        }

        if ($optionsii['rpstOn']=='1' && ($rpstEvrySecNew!=$rpstEvrySecEx || $optionsii['rpstTimes']!=$pval['rpstTimes'] || !$isRpstWasOn || ($isTD && $pval['rpstCustTD'][0]!=$optionsii['rpstNxTime']) || !empty($_POST['resetStats']))) {
            global $wpdb;
            $optionsii['rpstNxTime'] = $isTD?strtotime($pval['rpstCustTD'][0]):$nxs_cTime + $rpstEvrySecNew;
            $dbItem = array('datecreated'=>date_i18n('Y-m-d H:i:s'), 'type'=>'R', 'postid'=>$post_id, 'nttype'=>'', 'refid'=>'', 'timetorun'=> date_i18n('Y-m-d H:i:s', $optionsii['rpstNxTime']), 'extInfo'=>'', 'descr'=> 'Reposter ID:('.$post_id.')', 'uid'=>get_current_user_id());
            $wpdb->delete($wpdb->prefix . "nxs_query", array( 'postid' => $post_id ));
            $nxDB = $wpdb->insert($wpdb->prefix . "nxs_query", $dbItem);
            $lid = $wpdb->insert_id;
        }
        if (empty($optionsii['rpstOn'])) {
            global $wpdb;
            $wpdb->delete($wpdb->prefix . "nxs_query", array( 'postid' => $post_id ));
        }

        if (isset($pval['rpstTimes'])) {
            $optionsii['rpstTimes'] = trim($pval['rpstTimes']);
        }

        if (isset($pval['rpstType'])) {
            $optionsii['rpstType'] = trim($pval['rpstType']);
        }
        if (isset($pval['rpstTimeType'])) {
            $optionsii['rpstTimeType'] = trim($pval['rpstTimeType']);
        }
        if (isset($pval['rpstFromTime'])) {
            $optionsii['rpstFromTime'] = trim($pval['rpstFromTime']);
        }
        if (isset($pval['rpstToTime'])) {
            $optionsii['rpstToTime'] = trim($pval['rpstToTime']);
        }
        if (isset($pval['rpstOLDays'])) {
            $optionsii['rpstOLDays'] = trim($pval['rpstOLDays']);
        }
        if (isset($pval['rpstNWDays'])) {
            $optionsii['rpstNWDays'] = trim($pval['rpstNWDays']);
        }
        if (isset($pval['rpstOnlyPUP'])) {
            $optionsii['rpstOnlyPUP'] = trim($pval['rpstOnlyPUP']);
        } else {
            $optionsii['rpstOnlyPUP'] = 0;
        }
        if (isset($pval['rpstOnlyUniq'])) {
            $optionsii['rpstOnlyUniq'] = trim($pval['rpstOnlyUniq']);
        } else {
            $optionsii['rpstOnlyUniq'] = 0;
        }


        if (isset($pval['rpstBtwHrsType'])) {
            $optionsii['rpstBtwHrsType'] = trim($pval['rpstBtwHrsType']);
        }
        if (isset($pval['rpstBtwHrsT'])) {
            $optionsii['rpstBtwHrsT'] = trim($pval['rpstBtwHrsT']);
        }
        if (isset($optionsii['rpstBtwHrsT'])&&(int)$optionsii['rpstBtwHrsT']>23) {
            $optionsii['rpstBtwHrsT'] = 23;
        }
        if (isset($pval['rpstBtwHrsF'])) {
            $optionsii['rpstBtwHrsF'] = trim($pval['rpstBtwHrsF']);
        }
        if (isset($optionsii['rpstBtwHrsF'])&&(int)$optionsii['rpstBtwHrsF']>23) {
            $optionsii['rpstBtwHrsF'] = 23;
        }
        if (isset($pval['rpstBtwDays'])) {
            $optionsii['rpstBtwDays'] = $pval['rpstBtwDays'];
        } else {
            $optionsii['rpstBtwDays'] = array();
        }

        if (empty($optionsii['lastID']) || !empty($_POST['resetStats'])) {
            if (empty($optionsii['rpstType']) || $optionsii['rpstType']=='2') {
                $optionsii['lastID'] = 0;
            } elseif ($optionsii['rpstType']=='3') {
                $optionsii['lastID'] = 999999999;
            }
        }
        //  prr($pval);   prr($optionsii);
        self::save_meta($post_id, 'nxs_rpstr', $optionsii);
        return $optionsii;
    }

    public static function getStats($postID, $filter='', $rpstr='')
    {
        $stats = maybe_unserialize(get_post_meta($postID, 'nxs_rpstr_stats', true));
        $currTime = time() + (get_option('gmt_offset') * HOUR_IN_SECONDS);
        $phTxt = '';
        if (empty($filter)) {
            $filter = maybe_unserialize(get_post_meta($postID, 'nxs_rpstr_data', true));
        }
        if (empty($rpstr)) {
            $rpstr = maybe_unserialize(get_post_meta($postID, 'nxs_rpstr', true));
        }
        if (empty($stats)) {
            $stats = array('posts'=>0, 'posted'=>0, 'tbPosted'=>0, 'atPosted'=>0, 'fltText'=>'', 'statsText'=>'', 'nxTime'=>'', 'lpTime'=>'', 'lpID'=>'');
        }
        if (!empty($filter)) {
            nxs_removeAllWPQueryFilters();
            $ids = get_posts_ids_by_filter($filter);
            $pCnt = count($ids);
            $stats['posts']=$pCnt;
            $stats['fltText'] = nxsAnalyzePostFilters($filter);
        }
        $stats['nxTime'] = !empty($rpstr['rpstNxTime'])?date_i18n('Y-m-d H:i', $rpstr['rpstNxTime']):__('Never', 'social-networks-auto-poster-facebook-twitter-g');
        $stats['lTime'] = !empty($rpstr['rpstLastShTime'])?date_i18n('Y-m-d H:i', $rpstr['rpstLastShTime']):__('Never', 'social-networks-auto-poster-facebook-twitter-g');
        $stats['lpID'] = !empty($rpstr['rpstLastPostID'])?$rpstr['rpstLastPostID']:'';
        if (!empty($stats['pstdLst'])) {
            $ph = array_slice(array_reverse($stats['pstdLst']), 0, 10);
            $phTxt = '<b>Posting History</b> (Last 10 records): <br/>';
            foreach ($ph as $p) {
                $phTxt .=  $p.' - '.get_the_title($p).'<br/>';
            }
        }
        $stats['statsText'] = '<b>'.__('Total Posts', 'social-networks-auto-poster-facebook-twitter-g').':</b> '.$stats['posts'].'&nbsp;|&nbsp;<b>'.__('Posted', 'social-networks-auto-poster-facebook-twitter-g').':</b> '.$stats['posted']./*'&nbsp;|&nbsp;<b>'.__('To be Posted','social-networks-auto-poster-facebook-twitter-g').':</b> '.$stats['tbPosted'].*/' &nbsp;|&nbsp; <b>'.__('Next post will be', 'social-networks-auto-poster-facebook-twitter-g').' ~</b>&nbsp;'.$stats['nxTime'].'&nbsp;|&nbsp;'.__('Current Wordpress Time', 'social-networks-auto-poster-facebook-twitter-g').': '.date_i18n('Y-m-d H:i', $currTime).'<br>'. $stats['fltText'].'<br/>'.(!empty($stats['lpID'])?'<b>'.__('Last post', 'social-networks-auto-poster-facebook-twitter-g').'</b>&nbsp;(ID:&nbsp;'.$stats['lpID'].')&nbsp;<b>'.__('was reposted on:', 'social-networks-auto-poster-facebook-twitter-g').'</b>&nbsp;'.$stats['lTime']:'').(!empty($phTxt)?$phTxt:'');
        self::save_meta($postID, 'nxs_rpstr_stats', $stats);
        return $stats;
    }

    public static function print_title_metabox($current_post)
    {
        $options = (!empty($current_post))?maybe_unserialize(get_post_meta($current_post->ID, 'nxs_rpstr', true)):'';
        $ii = !empty($options['ii'])?$options['ii']:'0';
        if (!empty($current_post)) {
            $stats = self::getStats($current_post->ID, '', $options);
        } ?>
    <input value="1"  id="riC<?php echo $ii; ?>" <?php if (!isset($options['rpstOn']) || trim($options['rpstOn'])=='1') {
            echo "checked";
        } ?> type="checkbox" name="nxs_rpstr[rpstOn]"/>
       <b style="font-size: 16px;"><?php _e('Activate this Reposter Action', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b> <br />

    <br /><h4 style=" margin: 0px;float: left;"> <?php _e('Reposter Action Title:', 'social-networks-auto-poster-facebook-twitter-g'); ?></h4>
    <div id="titlediv"><div id="titlewrap"><label class="screen-reader-text" id="title-prompt-text" for="title">Enter title here</label>
      <input type="text" name="post_title" size="30" value="<?php echo (!empty($current_post))?$current_post->post_title:''; ?>" id="title" autocomplete="off">
    </div></div><br/>

    <b style="font-size: 15px;vertical-align: middle;"><?php _e('Get posts:', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>
       <select id="riS<?php echo $ii; ?>" name="nxs_rpstr[rpstType]" onchange="nxs_actDeActTurnOff(jQuery(this).attr('id'));">
        <option value="2" <?php  if (isset($options['rpstType']) && $options['rpstType']=='2') {
            echo 'selected="selected"';
        } ?>>One By One - Old to New</option>
        <option value="3" <?php if (isset($options['rpstType']) && $options['rpstType']=='3') {
            echo 'selected="selected"';
        } ?>>One By One - New to Old</option>
        <?php if (function_exists('nxs_doSMAS42')) {
            nxs_doSMAS42($options);
        } else {
            ?> <option disabled="disabled">[Pro Only] Random</option> <?php
        } ?>
       </select>
       <br/>
       <input value="1"  id="riOC<?php echo $ii; ?>" <?php if (isset($options['rpstOnlyPUP']) && trim($options['rpstOnlyPUP'])=='1') {
            echo "checked";
        } ?> type="checkbox" name="nxs_rpstr[rpstOnlyPUP]"/><b><?php _e('Post ONLY never autoposted posts', 'social-networks-auto-poster-
      facebook-twitter-g'); ?></b> - <i><?php _e('Will post only posts that were never posted by SNAP. General setting, will not be reset. This is usefull if you have existing posts and want them to be posted to your social media accounts only once.', 'social-networks-auto-poster-
      facebook-twitter-g'); ?></i> <br/>
      <span id="riS<?php echo $ii; ?>nxsRUQ" style="display: none;"><input value="1"  id="riOCU<?php echo $ii; ?>" <?php if (isset($options['rpstOnlyUniq']) && trim($options['rpstOnlyUniq'])=='1') {
            echo "checked";
        } ?> type="checkbox" name="nxs_rpstr[rpstOnlyUniq]"/><b><?php _e('Post each post only once', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>  - <i><?php _e('This will only post posts that were never posted by this reposter. If you reset reposter all posts will become available for reposting again.', 'social-networks-auto-poster-
      facebook-twitter-g'); ?></i> <br/></span>

      <?php if (!empty($current_post)) {
            ?>
        <div style="margin-top: 15px"> <input type="button" id="svBtn" onclick="nxs_svRep('<?php echo $current_post->ID; ?>')" class="button-primary" value="Save Reposter" /> </div>
      <?php
        }
        if (!empty($current_post)) {
            ?>

      <div><h3><?php _e('Info/Stats', 'social-networks-auto-poster-facebook-twitter-g'); ?>:&nbsp;<span style="font-size: 12px;">[<a href="#" data-rid="<?php echo $current_post->ID; ?>" id="nxs_rstRPStats"><?php _e('Reset Reposter', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>]</span></h3><img id="nxsSaveLoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /><div id="nxsSNAP_rpstrStats"><?php echo $stats['statsText']; ?></div></div>

    <?php
        }
    }
    public static function print_schedule_metabox($current_post)
    {
        ?>

   <?php $cr = get_option('NXS_cronCheck');
        if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') {
            global $nxs_SNAP;
            if (!isset($nxs_SNAP)) {
                return;
            }
            $gOptions = $nxs_SNAP->nxs_options;
            if (isset($gOptions['forceBrokenCron']) && $gOptions['forceBrokenCron'] =='1') {
                ?>
         <span style="color: red"> <?php _e('Your WP Cron is not working correctly. Auto Reposting service is active by force. <br/> This might cause problems. Please see the test results and recommendations', 'social-networks-auto-poster-facebook-twitter-g'); ?>
         &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl;
                echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span>
        <?php
            } else {
                ?> <span style="color: red"> <?php _e('Auto Reposting service is Disabled. Your WP Cron is not working correctly. Please see the test results and recommendations', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl;
                echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span>
   <?php return;
            }
        } ?>

   <?php $options = (!empty($current_post))?maybe_unserialize(get_post_meta($current_post->ID, 'nxs_rpstr', true)):'';
        $ii = !empty($options['ii'])?$options['ii']:'0';
        $nt = !empty($options['nt'])?$options['nt']:'0'; ?>

   <div class="nxs_tls_bdX">
     <?php if (function_exists('ns_SMASV41')) {
            ?> <input type="radio" name="nxs_rpstr[rpstTimes]" value="A" class="rpstrTimes" <?php if (empty($options['rpstTimes']) || $options['rpstTimes']=='A') {
                echo 'checked="checked"';
            } ?> /><?php
        } ?><b><?php _e('Post every:', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b>
     <input type="text" name="nxs_rpstr[rpstDays]" style="width: 35px;" value="<?php echo isset($options['rpstDays'])?$options['rpstDays']:'0'; ?>" />&nbsp;<?php _e('Days', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;
     <input type="text" name="nxs_rpstr[rpstHrs]" style="width: 35px;" value="<?php echo isset($options['rpstHrs'])?$options['rpstHrs']:'2'; ?>" />&nbsp;<?php _e('Hours', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;
     <input type="text" name="nxs_rpstr[rpstMins]" style="width: 35px;" value="<?php echo isset($options['rpstMins'])?$options['rpstMins']:'0'; ?>" />&nbsp;<?php _e('Minutes', 'social-networks-auto-poster-facebook-twitter-g'); ?>

     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <b><?php _e('Randomize posting time: &#177;', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b>
     <input type="text" name="nxs_rpstr[rpstRndMins]" style="width: 35px;" value="<?php echo isset($options['rpstRndMins'])?$options['rpstRndMins']:'15'; ?>" onmouseout="hidePopShAtt('RPST1');" onmouseover="showPopShAtt('RPST1', event);" />&nbsp;<?php _e('Minutes', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     <div id="rpstPostEveryOptions" style="margin-left:20px;display:<?php echo (empty($options['rpstTimes']) || $options['rpstTimes']=='A')?'block':'none'; ?>;">
       <div id="rpstPostWhenFinished" style="display:<?php echo (empty($options['rpstType']) || $options['rpstType']!='1')?'block':'none'; ?>;">
       <b><?php _e('When finished', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b>&nbsp;  <?php if (function_exists('nxs_v4doSMAS412')) {
            nxs_v4doSMAS412($options);
        } else {
            ?> <?php _e('Turn Reposting Off', 'social-networks-auto-poster-facebook-twitter-g') ?>
        <input type="hidden" name="nxs_rpstr[rpstStop]" value="O"/> <?php
        } ?>
       </div>
       <?php if (function_exists('nxs_v4doSMAS41')) {
            nxs_v4doSMAS41($options);
        } ?>
     </div>

     <?php if (function_exists('nxs_v4doSMAS413')) {
            nxs_v4doSMAS413($options);
        } else {
            ?>
       <div style="border: 2px dashed #ddd; border-radius: 3px; padding: 5px; margin-bottom: 8px; margin-top: 8px;"><b style="color: #008000"><?php  _e('[Pro Only]', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>&nbsp;<?php
         _e('You can chose what to do when reposting is finished: "Stop", "Repeat", or "Wait for new posts". You can set reposting only to specific days and hours.', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>   <?php
        } ?>

     </div>

    <?php
    }

    public static function print_networks_metabox($cp)
    {
        global $nxs_snapAvNts, $nxs_SNAP;
        if (!isset($nxs_SNAP)) {
            return;
        }
        $networks = $nxs_SNAP->nxs_accts; ?>
      <div style="float: right; font-size: 12px;" >
        <a href="#" onclick="jQuery('.nxsNPDoChb').attr('checked','checked'); return false;"><?php  _e('Check All', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;<a href="#" onclick="jQuery('.nxsNPDoChb').removeAttr('checked'); return false;"><?php _e('Uncheck All', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>
      </div>
      <div class="nxsNPRow" style="font-size: 12px;"> <?php
        foreach ($nxs_snapAvNts as $avNt) {
            $clName = 'nxs_snapClass'.$avNt['code'];
            $ntClInst = new $clName();
            if (isset($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) {
                ?>
            <div class="nxsNPRowGroup">
            <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo $avNt['lcode']; ?>16.png);"><?php echo $avNt['name']; ?></div>
            <?php $ntOpts = $networks[$avNt['lcode']];
                foreach ($ntOpts as $indx=>$pbo) {
                    $savedMeta = (!empty($cp))?maybe_unserialize(get_post_meta($cp->ID, 'nxs_rpstr_data', true)):'';
                    if (!empty($cp) && !empty($savedMeta)) {
                        $svdNTs=!empty($savedMeta['nxs_NPNts'])?$savedMeta['nxs_NPNts']:'';
                        $isCh=!empty($svdNTs)&&!empty($svdNTs[strtolower($avNt['code'])][$indx])&&$svdNTs[strtolower($avNt['code'])][$indx] == '1';
                    } else {
                        $isCh = (int)$pbo['do'] == 1;
                    } ?>
              <div class="nxsNPRowLine">
                <input class="nxsNPDoChb" value="<?php echo $avNt['lcode']; ?>--<?php echo $indx; ?>" name="nxs_NPNts[]" type="checkbox" <?php if ($isCh) {
                        echo "checked";
                    } ?> />
                <?php echo $avNt['name']; ?> <i style="color: #005800;"><?php if ($pbo['nName']!='') {
                        echo "(".$pbo['nName'].")";
                    } ?></i>
              </div>
            <?php
                } ?></div><?php
            }
        } ?>
      </div>
    <?php
    }

    public static function print_posts_metabox($current_post, $nt='', $ii='', $metaSettings='')
    {
        if (is_array($nt)) {
            $nt = '';
        }
        $ntN = $nt.$ii;
        if (empty($ntN)) {
            wp_nonce_field(basename(__FILE__), 'nxs_metabox_nonce');
        }

        //## Get saved Settinsg
        if (empty($metaSettings) && !empty($current_post->ID)) {
            $metaSettings = maybe_unserialize(get_post_meta($current_post->ID, 'nxs_rpstr_data', true));
        }             //  prr($metaSettings, 'FLTRSZZ');
        /*
        echo '<h2>'. __( 'How to get posts', self::$plugin_name ) .'</h2><div class="nxsLftPad"></div>';

         ?> <span style="font-size: 13px;">&nbsp;&nbsp;&nbsp;<?php _e('Please note: All criterias are connected with "AND"', 'social-networks-auto-poster-facebook-twitter-g'); ?></span>
        <?php     */
        //## Print Filter sections ?><div id="addCriNewPool"><?php
             self::print_catsTags_section($current_post, $metaSettings, $nt, $ii);
        if (empty($ntN) && empty($metaSettings['fltrsOn'])) {
            self::print_timeframe_section($current_post, $metaSettings, $nt, $ii);
            self::print_dates_section($current_post, $metaSettings, $nt, $ii);
        }

        self::print_types_section($current_post, $metaSettings, $nt, $ii);
        self::print_author_section($current_post, $metaSettings, $nt, $ii);
        if (empty($ntN) && empty($metaSettings['fltrsOn'])) {
            self::print_postsPages_section($current_post, $metaSettings, $nt, $ii);
        }
        if (function_exists('ns_SMASV41')) {
            self::print_search_section($current_post, $metaSettings, $nt, $ii);
            self::print_meta_section($current_post, $metaSettings, $nt, $ii);
            self::print_taxonomies_section($current_post, $metaSettings, $nt, $ii);
            self::print_lang_section($current_post, $metaSettings, $nt, $ii);
        } else {
            ?>
                 <div style="border: 2px dashed #ddd; border-radius: 3px; text-align: center; padding: 10px; margin-left: 40px; margin-right: 40px;"> <a href="https://www.nextscripts.com/social-networks-autoposter-wordpress-plugin-pro/" target="_blank">SNAP Pro version</a> can also filter by Custom Fields, Custom Taxonomies, and Searches. <br/> Please see more here: <a href="https://www.nextscripts.com/snap-features/filters" target="_blank">Filters</a></div>
             <?php
        } ?></div><?php
    }

    public static function makeInputName($name, $nt='', $ii='')
    {
        $ntN = $nt.$ii;
        if (!empty($ntN)) {
            return $nt.'['.$ii.']['.$name.']';
        } else {
            return $name;
        }
    }
    //## Sections
    public static function print_catsTags_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $ntN = $nt.$ii;
        if (empty($metaSettings)) {
            $metaSettings = array();
        }// ## Categories and Tags
        $isVis =  !empty($metaSettings['nxs_tags_names']) || !empty($metaSettings['nxs_cats_names']); //prr($metaSettings);
        echo '<h4 onclick="jQuery(\'#nxs_sec_catsTags'.$ntN.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/tag24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;" >'.
          __('Categories and Tags', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_catsTags'.$ntN.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';
        //## Get Tags and Cats from DB
        $cat_names = array();
        $tags_names = array();
        $tagsCnt = wp_count_terms('post_tag');
        $catsCnt = wp_count_terms('category');
        if (empty($metaSettings['nxs_tags_names'])) {
            $metaSettings['nxs_tags_names'] = array();
        }
        if (empty($metaSettings['nxs_cats_names'])) {
            $metaSettings['nxs_cats_names'] = array();
        }
        $tags = !empty($metaSettings["nxs_tags_names"])?get_tags(array( 'hide_empty' => false, 'include'=>implode(',', $metaSettings["nxs_tags_names"]), 'number'=>500 )):'';
        $categories = !empty($metaSettings["nxs_cats_names"])?get_categories(array( 'hide_empty' => false, 'include'=>implode(',', $metaSettings["nxs_cats_names"]), 'number'=>500 )):'';
        if ($tags) {
            foreach ($tags as $tag) {
                $tags_names[$tag->term_id] = $tag->name;
            }
        }
        natsort($tags_names);
        if ($categories) {
            foreach ($categories as $category) {
                $cat_names[$category->term_id] = $category->name;
            }
        }
        natsort($cat_names);
        //## Checkboxes
        $selTI = empty($metaSettings['nxs_ie_tags_names']) ? 'checked="checked"' : '';
        $selCI = empty($metaSettings['nxs_ie_cats_names']) ? 'checked="checked"' : '';
        $selTE = $selTI=='' ? 'checked="checked"' : '';
        $selCE = $selCI=='' ? 'checked="checked"' : '';

        //echo '<div><label class="field_title">'. __( 'Categories', 'social-networks-auto-poster-facebook-twitter-g' ) . '('.$catsCnt.'):</label>';
        echo '<div><label class="field_title">'. __('Categories', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>';
        echo '&nbsp;<input type="radio" '.$selCI.' name="'.self::makeInputName('nxs_ie_cats_names', $nt, $ii).'" value="0">Include (Post only with..)&nbsp;&nbsp;<input type="radio" '.$selCE.' name="'.self::makeInputName('nxs_ie_cats_names', $nt, $ii).'" value="1">Exclude (Do not post ...)<br/>';
        self::print_select((!empty($current_post->ID))?$current_post->ID:0, $cat_names, 'nxs_cats_names'.$ntN, !empty($metaSettings['nxs_cats_names'])?$metaSettings['nxs_cats_names']:'', true, true, self::makeInputName('nxs_cats_names', $nt, $ii), 'nxsSelItAjx', 'category');
        echo '</div>';

        //echo '<div><label class="field_title">'. __( 'Tags', 'social-networks-auto-poster-facebook-twitter-g' ) . '('.$tagsCnt.'):</label>';
        echo '<div><label class="field_title">'. __('Tags', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>';
        echo '&nbsp;<input type="radio" '.$selTI.' name="'.self::makeInputName('nxs_ie_tags_names', $nt, $ii).'" value="0">Include&nbsp;&nbsp;<input type="radio" '.$selTE.' name="'.self::makeInputName('nxs_ie_tags_names', $nt, $ii).'" value="1">Exclude<br/>';
        self::print_select((!empty($current_post->ID))?$current_post->ID:0, $tags_names, 'nxs_tags_names'.$ntN, !empty($metaSettings['nxs_tags_names'])?$metaSettings['nxs_tags_names']:'', true, true, self::makeInputName('nxs_tags_names', $nt, $ii), 'nxsSelItAjxAdd', 'post_tag');
        echo '</div>';


        echo "</div>";
    }
    public static function print_timeframe_section($current_post, $metaSettings, $nt='', $ii='')
    {   // ## Dates
        $isVis =  !empty($metaSettings["nxs_starting_period"]);
        echo '<h4 onclick="jQuery(\'#nxs_sec_timeframe\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/time24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Timeframes (Exact Dates)', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_timeframe" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';

        $count_periods     = (!empty($current_post->ID) && !empty($metaSettings['nxs_count_date_periods']))?$metaSettings['nxs_count_date_periods']:'';
        if (empty($count_periods)) {
            $count_periods = 1;
        }

        echo '<div id="nxs_timeframeTopDiv'.$nt.$ii.'">';
        for ($i = 1; $i <= $count_periods; $i++) {
            $postfix = $i == 1 ? '' : '_'. $i;
            $rel     = $i == 1 ? '' : 'nxs_date_period_'. $i;
            $check_inclusive = (!empty($current_post->ID) && !empty($metaSettings["nxs_inclusive$postfix"]))?$metaSettings["nxs_inclusive$postfix"]:'';

            if ($i > 1) {
                echo '<br/>';
            }
            echo '<div class="nxs_short_field" id="nxs_timeframe_Div'.$nt.$ii.$postfix.'" rel="'. $rel .'"><select name="nxs_timeframe_incORExcl'. $postfix. '"><option value="i" selected="selected">Include</option><option value="e">Exclude</option></select>';
            echo '<span class="field_title">'. __(' posts from ', 'social-networks-auto-poster-facebook-twitter-g') .'</span>';
            echo '<input type="text" id="'. 'nxs_starting_period'. $postfix. '" name="'. 'nxs_starting_period'. $postfix. '" class="selectize-input datepicker" value="'. ((!empty($current_post->ID) && !empty($metaSettings["nxs_starting_period$postfix"]))?$metaSettings["nxs_starting_period$postfix"]:'') .'">';

            echo '<span class="field_title">'. __(' to ', 'social-networks-auto-poster-facebook-twitter-g') .'</span>';
            echo '<input type="text" id="'. 'nxs_end_period'. $postfix. '" name="'. 'nxs_end_period'. $postfix. '" class="selectize-input datepicker" value="'. ((!empty($current_post->ID) && !empty($metaSettings["nxs_end_period$postfix"]))?$metaSettings["nxs_end_period$postfix"]:'') .'">';

            echo '<input type="hidden" name="'. 'nxs_inclusive'. $postfix. '" id="'. 'nxs_inclusive'. $postfix. '"  value="1">';
            echo '<span style="padding:5px;"><button style="display:'.($i > 1 ?'inline-block':'none').'" name="nxs_remove_date_period'. $postfix. '" id="nxs_remove_date_period'. $postfix. '" class="nxs_remove_date_period">'. __('Remove', 'social-networks-auto-poster-facebook-twitter-g') .'</button></span>';

            echo '</div>';
        }
        echo '</div>';
        echo '<br/>';

        echo '<button data-ii="'.$ii.'" data-nt="'.$nt.'"  class="nxs_add_date_period">'. __('Add More', 'social-networks-auto-poster-facebook-twitter-g') .'...</button>';
        echo '<input type="hidden" id="nxs_count_date_periods" name="nxs_count_date_periods" value="'. $count_periods .'">';
        echo "</div>";
    }
    public static function print_dates_section($current_post, $metaSettings, $nt='', $ii='')
    { //## ABS DAtes
        $isVis =  !empty($metaSettings['absPeriods']);
        echo '<h4 onclick="jQuery(\'#nxs_sec_dates\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/time24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Dates - Older/Newer', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_dates" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';

        $count_abs_periods = (!empty($current_post->ID) && !empty($metaSettings['nxs_count_abs_periods']))?$metaSettings['nxs_count_abs_periods']:'';
        if (empty($count_abs_periods)) {
            $count_abs_periods = 1;
        }
        echo '<div id="nxs_abstimeTopDiv'.$nt.$ii.'">';

        for ($j = 1; $j <= $count_abs_periods; $j++) {
            $postfix = $j == 1 ? '' : '_'. $j;
            $rel = $j == 1 ? '' : 'nxs_abs_period_'. $j;
            $indx = $j-1;
            if ($j>1 && empty($metaSettings["absPeriods"][$indx])) {
                continue;
            }

            $types_abs_periods = array(
            'days'   => __('Days', 'social-networks-auto-poster-facebook-twitter-g'),
            'weeks'  => __('Weeks', 'social-networks-auto-poster-facebook-twitter-g'),
            'months' => __('Months', 'social-networks-auto-poster-facebook-twitter-g'),
            'years'  => __('Years', 'social-networks-auto-poster-facebook-twitter-g'),
            'minutes'=> __('Minutes', 'social-networks-auto-poster-facebook-twitter-g'),
            'hours'  => __('Hours', 'social-networks-auto-poster-facebook-twitter-g')
            );

            if ($j > 1) {
                echo '<br/>';
            }
            echo '<div class="nxs_short_fieldx" id="nxs_abstime_Div'.$nt.$ii.$postfix.'"  rel="'. $rel .'">';
            echo '<span class="field_title">'. __('Posts newer than', 'social-networks-auto-poster-facebook-twitter-g') .' </span>';
            echo '<input type="text" style="width:40px;" id="nxs_start_abs_period'. $postfix. '" name="nxs_start_abs_period'. $postfix. '" value="'. ((!empty($current_post->ID) && !empty($metaSettings["absPeriods"][$indx]['start']))?$metaSettings["absPeriods"][$indx]['start']:'') .'">';

            self::printSimpleSelect("nxs_start_abs_period_type$postfix", "nxs_start_abs_period_type$postfix", (!empty($metaSettings["absPeriods"][$indx])?$metaSettings["absPeriods"][$indx]['typeStart']:'days'), $types_abs_periods, 'NS');
            //self::print_select( (!empty( $current_post->ID))?$current_post->ID:0, $types_abs_periods, "typeStart", $metaSettings["absPeriods"][$indx], true, false, "nxs_start_abs_period_type$postfix",'NS' );
            echo '<span class="field_title">'. __(' and older than', 'social-networks-auto-poster-facebook-twitter-g') .' </span>';
            echo '<input type="text" style="width:40px;" id="nxs_end_abs_period'. $postfix. '" name="'. 'nxs_end_abs_period'. $postfix. '" class="selectize-input" value="'. ((!empty($current_post->ID) && !empty($metaSettings["absPeriods"][$indx]['end']))?$metaSettings["absPeriods"][$indx]['end']:'') .'">';
            //self::print_select( (!empty( $current_post->ID))?$current_post->ID:0, $types_abs_periods, "nxs_end_abs_period_type$postfix", $metaSettings, true, false,'','NS' );
            self::printSimpleSelect("nxs_end_abs_period_type$postfix", "nxs_end_abs_period_type$postfix", (!empty($metaSettings["absPeriods"][$indx])?$metaSettings["absPeriods"][$indx]['typeEnd']:'days'), $types_abs_periods, 'NS');

            echo '<span style="padding:5px;"><button style="display:'.($j > 1 ?'inline-block':'none').'" name="nxs_remove_abs_period'. $postfix. '" id="nxs_remove_abs_period'. $postfix. '" class="nxs_remove_abs_period">'. __('Remove', 'social-networks-auto-poster-facebook-twitter-g') .'</button></span>';

            echo '</div>';
        }
        echo '</div>';
        echo '<br/><button data-ii="'.$ii.'" data-nt="'.$nt.'"  id="nxs_add_abs_period">'. __('Add more', 'social-networks-auto-poster-facebook-twitter-g') .'...</button>';
        echo '<input type="hidden" id="nxs_count_abs_periods" name="nxs_count_abs_periods" value="'. $count_abs_periods .'">';
        echo "</div>";
    }
    public static function print_types_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $ntN = $nt.$ii;
        $isVis = !empty($metaSettings['nxs_post_status']) || !empty($metaSettings['nxs_post_type']) || !empty($metaSettings['nxs_post_formats']); // prr(self::$post_formats);
        //## Type, Status, Format
        echo '<h4 onclick="jQuery(\'#nxs_sec_types'.$ntN.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/type24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Post Type, Post Format', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_types'.$ntN.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';


        $formats = array();
        if (is_array(self::$post_formats)) {
            foreach (self::$post_formats[0] as $format) {
                $formats['post-format-'. $format] = $format;
            }
            $formats['standard'] = 'Standard';
        }


        $selCI = empty($metaSettings['nxs_ie_posttypes']) ? 'checked="checked"' : '';
        $selCE = $selCI=='' ? 'checked="checked"' : '';
        /*
        $posts_statuses = get_post_stati( 0, 'object');
        if( !empty( $posts_statuses ) ) { $translated_statuses = array(); foreach( $posts_statuses as $status ) $translated_statuses[$status->name] = $status->label;
            $translated_statuses['protected'] = __( 'Password Protected', self::$plugin_name ); natsort( $translated_statuses );
            echo '<div><label class="field_title">'. __( 'Status', self::$plugin_name ) . ':</label>&nbsp;&nbsp;<span class="description">'. __( 'Select posts by status' ) .'</span>';
            self::print_select( (!empty( $current_post->ID))?$current_post->ID:0, $translated_statuses, 'nxs_post_status', !empty($metaSettings['nxs_post_status'])?$metaSettings['nxs_post_status']:'', true, true, self::makeInputName('nxs_post_status', $nt, $ii) ); echo '</div>';
        }
        */
        if (!empty(self::$posts_types)) {
            if (empty($current_post)&&empty($metaSettings)&&empty($nt)&&empty($ii)) {
                $metaSettings = array('nxs_post_type' => array('0'=>'post'));
            }
            echo '<div><label class="field_title">'. __('Post Types (Posts, Pages, Custom Post Types)', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>';
            echo '&nbsp;<input type="radio" '.$selCI.' name="'.self::makeInputName('nxs_ie_posttypes', $nt, $ii).'" value="0">Include (Post only ...)&nbsp;&nbsp;<input type="radio" '.$selCE.' name="'.self::makeInputName('nxs_ie_posttypes', $nt, $ii).'" value="1">Exclude (Do not post ...)<br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, self::$posts_types, 'nxs_post_type', !empty($metaSettings['nxs_post_type'])?$metaSettings['nxs_post_type']:'', false, true, self::makeInputName('nxs_post_type', $nt, $ii));
            echo '</div>';
        }
        if (!empty($formats)) {
            echo '<div><label class="field_title">'. __('Formats', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Standard Blogpost, Image, Audio, Video, Status, Quote, etc..') .'</span><br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $formats, 'nxs_post_formats', !empty($metaSettings['nxs_post_formats'])?$metaSettings['nxs_post_formats']:'', true, true, self::makeInputName('nxs_post_formats', $nt, $ii));
            echo '</div>';
        }
        echo "</div>";
    }
    public static function print_author_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $isVis = !empty($metaSettings['nxs_user_names']);
        $ntN = $nt.$ii;
        // ## Author
        echo '<h4 onclick="jQuery(\'#nxs_sec_author'.$ntN.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/user24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Author', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_author'.$ntN.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';

        $user_names = array(); //$users = get_users();     //   prr($users); //## Not Good when we have a lot of subscribers.
        global $wpdb;
        $users = $wpdb->get_results("SELECT ID, user_login, display_name FROM $wpdb->users WHERE 1=1 AND {$wpdb->users}.ID IN (SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities' AND {$wpdb->usermeta}.meta_value NOT LIKE '%subscriber%') ORDER BY display_name ASC"); //prr($users);

        if ($users) {
            foreach ($users as $user) {
                $user_names[$user->ID] = $user->display_name." (".$user->user_login.")";
            }
        }
        if (!empty($user_names)) {
            echo '<div><label class="field_title" for="'. 'nxs_user_names">'. __('Author', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Author') .'</span><br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $user_names, 'nxs_user_names', !empty($metaSettings['nxs_user_names'])?$metaSettings['nxs_user_names']:'', true, true, self::makeInputName('nxs_user_names', $nt, $ii));
            echo '</div>';
        }
        echo "</div>";
    }
    public static function print_postsPages_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $isVis = !empty($metaSettings['nxs_post_ids']) || !empty($metaSettings['nxs_post_ids']) || !empty($metaSettings['nxs_post_ids']);
        // ## Exact Posts and Pages
        echo '<h4 onclick="jQuery(\'#nxs_sec_postsPages\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/post24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
         __('Exact Posts and Pages', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_postsPages" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';

        $post_names = array(); //$users = get_users();     //   prr($users); //## Not Good when we have a lot of subscribers.
        global $wpdb; // $posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE 1=1 ORDER BY post_title ASC LIMIT 500"); //prr($users);
        $posts = get_posts(array('orderby'=>'title', 'post_status' => array( 'pending', 'publish', 'future' ), 'post_type' =>  'any', 'posts_per_page'=>100, 'post__in' => (!empty($metaSettings['nxs_post_ids'])?$metaSettings['nxs_post_ids']:array( ))));
        if ($posts) {
            foreach ($posts as $post) {
                $post_names[$post->ID] = "[ID: ".$post->ID."] ".$post->post_title;
            }
        }
        if (!empty($post_names)) {
            echo '<div><label class="field_title" for="'. 'nxs_post_ids">'. __('Post/Page/Custom Post Type', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('') .'</span><br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $post_names, 'nxs_post_ids', !empty($metaSettings['nxs_post_ids'])?$metaSettings['nxs_post_ids']:'', true, true, self::makeInputName('nxs_post_ids', $nt, $ii), 'nxsSelItAjx', 'post');
            echo '</div>';
        }
        echo "</div>";
    }

    public static function print_postsPagesX_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $isVis = !empty($metaSettings['nxs_name_post']) || !empty($metaSettings['nxs_name_page']) || !empty($metaSettings['nxs_name_parent']);
        // ## Exact Posts and Pages
        echo '<h4 onclick="jQuery(\'#nxs_sec_postsPages\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/post24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
         __('Exact Posts and Pages', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_postsPages" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';

        $posts_names = array();
        $posts_parents = array();
        $pages_names = array();

        if (!empty(self::$posts)) {
            foreach (self::$posts as $post) {
                if (in_array($post->post_type, self::$posts_types) && $post->post_type != 'nxs_filter') {
                    if (!empty($post->post_title) && $post->post_type == 'page') {
                        $pages_names[$post->ID] = $post->post_title;
                    }

                    if (!empty($post->post_title) && $post->post_type != 'page' && $post->post_type != 'attachment') {
                        $posts_names[$post->ID] = $post->post_title;
                    }

                    if (!empty($post->post_parent) && self::search_post_by_id($post->post_parent)) {
                        $posts_parents[$post->post_parent] = self::search_post_by_id($post->post_parent);
                    }
                }
            }
        }


        echo '<div><label class="field_title">'. __('Post/Page/Custom Post Type', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Select post') .'</span>';
        self::print_select((!empty($current_post->ID))?$current_post->ID:0, $posts_names, 'nxs_name_post', !empty($metaSettings['nxs_name_post'])?$metaSettings['nxs_name_post']:'', true);
        echo '</div>';



        if (!empty($posts_names)) {
            echo '<div><label class="field_title">'. __('Post Name', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Select post by name') .'</span>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $posts_names, 'nxs_name_post', !empty($metaSettings['nxs_name_post'])?$metaSettings['nxs_name_post']:'', true);
            echo '</div>';
        }

        if (!empty($pages_names)) {
            echo '<div><label class="field_title">'. __('Page Name', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Select page by name') .'</span>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $pages_names, 'nxs_name_page', !empty($metaSettings['nxs_name_page'])?$metaSettings['nxs_name_page']:'', true);
            echo '</div>';
        }

        if (!empty($posts_parents)) {
            echo '<div><label class="field_title">'. __('Page/Post Parent Name', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Select Page/Post by Parent Name') .'</span>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $posts_parents, 'nxs_name_parent', !empty($metaSettings['nxs_name_parent'])?$metaSettings['nxs_name_parent']:'', true);
            echo '</div>';
        }



        echo "</div>";
    }
    public static function print_search_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $isVis = !empty($metaSettings['nxs_search_keywords']);
        // ## Search
        echo '<h4 onclick="jQuery(\'#nxs_sec_search'.$nt.$ii.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/search24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Search', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_search'.$nt.$ii.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">'; ?>
        <div>
            <label class="field_title"><?php _e('Search', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</label>&nbsp;&nbsp;<span class="description"><?php _e('Please enter the search query') ?></span> <br/>
            <input type="text" name="<?php echo self::makeInputName('nxs_search_keywords', $nt, $ii); ?>" class="selectize-input" autocomplete="off" placeholder="Please enter the search query..." value="<?php echo ($isVis)?$metaSettings['nxs_search_keywords']:''; ?>">
        </div> <?php
        echo "</div>";
    }
    public static function print_meta_section($current_post, $metaSettings, $nt='', $ii='')
    {
        if (empty($metaSettings)) {
            $metaSettings = array();
        }
        if (empty($metaSettings['post_meta'])) {
            $metaSettings['post_meta'] = array();
        }
        //## "Repost ONLY previously unautoposted posts " checkbox - remoive from visible interface
        if (!empty($metaSettings['post_meta'])) {
            foreach ($metaSettings['post_meta'] as $kk=>$vk) {
                if ($vk['key']=='snap_isAutoPosted') {
                    unset($metaSettings['post_meta'][$kk]);
                }
            }
        }
        //## Show Post Meta section.
        $isVis = !empty($metaSettings['post_meta']);
        $ntN = $nt.$ii;
        echo '<h4 onclick="jQuery(\'#nxs_sec_meta'.$nt.$ii.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/meta24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Custom Fields', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_meta'.$nt.$ii.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';
        $post_meta_keys   = array();
        $post_meta_values = array();
        $type_options = array('NUMERIC', 'BINARY', 'DATE', 'CHAR', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED');
        //$count_compares   = (!empty( $current_post->ID))?$metaSettings['nxs_count_meta_compares']:'';
        $count_compares = !empty($metaSettings['post_meta'])?count($metaSettings['post_meta']):1;
        $relation_options = array( 'AND', 'OR' );
        $compare_options  = array('='=>'=', '!='=>'!=', 'gt'=>'>', 'gt='=>'>=', 'lt'=>'<', 'lt='=>'<=', 'LIKE'=>'LIKE', 'NOT LIKE'=>'NOT LIKE', 'IN'=>'IN', 'NOT IN'=>'NOT IN', 'BETWEEN'=>'BETWEEN', 'NOT BETWEEN'=>'NOT BETWEEN', 'EXISTS'=>'EXISTS', 'NOT EXISTS'=>'NOT EXISTS');
        echo '<div id="nxs_meta_namesTopDiv'.$nt.$ii.'">';

        for ($i = 1; $i <= $count_compares; $i++) {
            $postfix = $i == 1 ? '' : '_'. $i;
            $rel = $i == 1 ? '' : 'nxs_meta_compare_'. $i;
            $jj = $i-1;
            if (empty($metaSettings['post_meta'][$jj])) {
                $metaSettings['post_meta'][$jj] = array();
            }
            if ($i>1 && empty($metaSettings['post_meta'][$jj]["key"])) {
                continue;
            }
            echo '<div class="nxs_metas_panel"  id="nxs_meta_namesDiv'.$nt.$ii.$postfix.'"><hr/>';
            echo '<div class="nxs_metas_leftPanel" style="display:'.(($i>1)?'inline-block':'none').';">';
            echo '<button class="nxs_remove_meta_compare">'. __('Remove', 'social-networks-auto-poster-facebook-twitter-g') .'</button>';
            echo '</div><div class="nxs_metas_rightPanel">'; ?>
        <div class="">
          <div class="">
            <div class="nxs_medium_field_txn"> <label class="field_title"> <?php _e('Custom Field Name', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</label><br/>
<input name="<?php echo self::makeInputName("nxs_meta_key$postfix", $nt, $ii); ?>" id="nxs<?php echo $nt.$ii; ?>_meta_key<?php echo $postfix; ?>" value="<?php echo !empty($metaSettings['post_meta'][$jj]["key"])?$metaSettings['post_meta'][$jj]["key"]:''; ?>"  style="font-weight: bold; color: #005800; border: 1px solid #ACACAC; width: 95%;"/>
            </div>
            <div class="nxs_shortXL_field"><?php
            echo '<div class="'. 'nxs_short_field" rel="'. $rel .'">';
            echo '<label class="field_title">'. __('Operator', 'social-networks-auto-poster-facebook-twitter-g') . ':</label><br/>'; //prr($metaSettings['post_meta'][$jj]["operator"]);
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $compare_options, "nxs".$nt.$ii."_meta_operator$postfix", !empty($metaSettings['post_meta'][$jj]["operator"])?$metaSettings['post_meta'][$jj]["operator"]:'', false, false, self::makeInputName("nxs_meta_operator$postfix", $nt, $ii), 'hui');
            echo '</div>'; ?></div>
            <div class="nxs_mediumXL_field_txn"><label class="field_title"> <?php _e('Custom Field Value', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</label><br/>
                <input name="<?php echo self::makeInputName("nxs_meta_value$postfix", $nt, $ii); ?>[]" id="nxs<?php echo $nt.$ii; ?>_meta_value<?php echo $postfix; ?>" style="font-weight: bold; color: #005800; border: 1px solid #ACACAC; width: 95%;" value="<?php echo !empty($metaSettings['post_meta'][$jj]["value"])?$metaSettings['post_meta'][$jj]["value"][0]:''; ?>"/>
            </div>
          </div>
          <div class="">
          </div>
        </div>
        <?php

        if ($i==1) {
            echo '<div class="nxs_short_field" id="nxs_meta_namesCond'.$nt.$ii.'" style="display:'.(($count_compares>1)?'inline-block':'none').';"><hr/>';
            echo '<label class="field_title">'. __('Condition', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>';
            //self::print_select( (!empty( $current_post->ID))?$current_post->ID:0, $relation_options, "nxs".$nt.$ii."_meta_relation", !empty($metaSettings["nxs_meta_relation$postfix"])?$metaSettings["nxs_meta_relation$postfix"]:'', false, false, self::makeInputName("nxs_meta_relation$postfix", $nt, $ii ), 'hiu');
            self::printSelect("nxs".$nt.$ii."_meta_relation", self::makeInputName("nxs_meta_relation", $nt, $ii), false, $relation_options, !empty($metaSettings['post_meta'][0]["relation"])?$metaSettings['post_meta'][0]["relation"]:'', !empty($current_post->ID)?$current_post->ID:0, false, 'notTknz');
            echo '</div>';
        } ?>

</div>
    </div>
        <?php
        }

        echo '</div>';

        echo '<div style="padding-top:25px;"><button data-ii="'.$ii.'" data-nt="'.$nt.'" class="nxs_add_meta_compare">'. __('Add More', 'social-networks-auto-poster-facebook-twitter-g') .'...</button></div>';
        echo '<input type="hidden" id="nxs_count_meta_'.$nt.$ii.'" name="'.self::makeInputName('nxs_count_meta_compares', $nt, $ii).'" value="'. $count_compares .'">';

        echo "</div>";
    }

    public static function print_taxonomies_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $isVis = !empty($metaSettings['nxs_term_names']); // prr($metaSettings); //  $metaSettings['nxs_count_term_compares'] = 1;
        //## Taxonomies
        echo '<h4 onclick="jQuery(\'#nxs_sec_taxonomies'.$nt.$ii.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/tag24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;" >'.
           __('Taxonomies', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;&nbsp;&gt;&gt; </h4><div id="nxs_sec_taxonomies'.$nt.$ii.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';
        $taxs_names = self::$taxonomies;
        $relation_options = array( 'AND', 'OR' );
        $compare_options  = array( 'IN', 'NOT IN', 'AND' );
        $children_options = array('no' => __('No', 'social-networks-auto-poster-facebook-twitter-g'), 'yes' => __('Yes', 'social-networks-auto-poster-facebook-twitter-g'));
        $count_compares = !empty($metaSettings['nxs_count_term_compares'])?$metaSettings['nxs_count_term_compares']:''; //prr($metaSettings['nxs_count_term_compares']);
        unset($taxs_names['post_format']);
        $terms_names = array();

        if (empty($count_compares)) {
            $count_compares = 1;
        }
        echo '<div id="nxs_term_namesTopDiv'.$nt.$ii.'">';

        for ($i = 1; $i <= $count_compares; $i++) {
            $postfix = $i == 1 ? '' : '_'. $i;
            $rel = $i == 1 ? '' : 'nxs_term_compare_'. $i;
            if ($i>1 && empty($metaSettings["nxs_term_names$postfix"])) {
                continue;
            }
            echo '<div class="nxs_terms_panel"  id="nxs_term_namesDiv'.$nt.$ii.$postfix.'"><hr/>';

            echo '<div class="nxs_terms_leftPanel" style="display:'.(($i>1)?'block':'none').';">';

            echo '<button class="nxs_remove_term_compare">'. __('Remove', 'social-networks-auto-poster-facebook-twitter-g') .'</button>';
            echo '</div><div class="nxs_terms_rightPanel">';


            echo '<div>';
            //## Get already selected terms
            if (!empty($metaSettings["nxs_tax_names$postfix"])) {
                $terms_names = array();
                $terms = !empty($metaSettings["nxs_term_names$postfix"])?get_terms($metaSettings["nxs_tax_names$postfix"], array( 'hide_empty' => false, 'include'=>implode(',', $metaSettings["nxs_term_names$postfix"]), 'number'=>500)):'';
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $terms_names[$term->term_id] = $term->name;
                    }
                    natsort($terms_names);
                } else {
                    $terms_names = array();
                }
            }
            echo '<div class="nxs_medium_field_txn" rel="'. $rel .'">';
            echo '<label class="field_title">'. __('Taxonomy', 'social-networks-auto-poster-facebook-twitter-g') . ':</label><br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $taxs_names, "nxs".$nt.$ii."_tax_names$postfix", !empty($metaSettings["nxs_tax_names$postfix"])?$metaSettings["nxs_tax_names$postfix"]:'', true, false, self::makeInputName("nxs_tax_names$postfix", $nt, $ii), 'nxs_tax_names');
            echo '</div>';

            echo '<div class="'. 'nxs_short_field" rel="'. $rel .'">';
            echo '<label class="field_title">'. __('Operator', 'social-networks-auto-poster-facebook-twitter-g') . ':</label><br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $compare_options, "nxs".$nt.$ii."_term_operator$postfix", !empty($metaSettings["nxs_term_operator$postfix"])?$metaSettings["nxs_term_operator$postfix"]:'', false, false, self::makeInputName("nxs_term_operator$postfix", $nt, $ii), 'hui');
            echo '</div>';

            echo '<div class="'. 'nxs_short_field" rel="'. $rel .'">';
            echo '<label class="field_title">'. __('Child Terms', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $children_options, "nxs".$nt.$ii."_term_children$postfix", !empty($metaSettings["nxs_term_children$postfix"])?$metaSettings["nxs_term_children$postfix"]:'', true, false, self::makeInputName("nxs_term_children$postfix", $nt, $ii), 'hiu');
            echo '</div>';

            echo '<br/><div class="nxs_medium_field_txnNL" rel="'. $rel .'">';
            echo '<label class="field_title">'. __('Terms', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Select Terms') .'</span>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $terms_names, 'nxs'.$nt.$ii."_term_names$postfix", !empty($metaSettings["nxs_term_names$postfix"])?$metaSettings["nxs_term_names$postfix"]:'', true, true, self::makeInputName("nxs_term_names$postfix", $nt, $ii), 'nxsSelItAjxAdd', !empty($metaSettings["nxs_tax_names$postfix"])?$metaSettings["nxs_tax_names$postfix"]:'');
            echo '</div></div>';

            if ($i==1) {
                echo '<div class="nxs_short_field" id="nxs_term_namesCond'.$nt.$ii.'" style="'.(($count_compares < 2)?'display:none;':'').'">';
                echo '<label class="field_title">'. __('Condition', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>';
                self::printSelect("nxs".$nt.$ii."_term_relation", self::makeInputName("nxs_term_relation", $nt, $ii), false, $relation_options, !empty($metaSettings["nxs_term_relation"])?$metaSettings["nxs_term_relation"]:'', !empty($current_post->ID)?$current_post->ID:0, false, 'notTknz');
                echo '</div>';
            }


            echo '</div></div>';
        }

        echo '</div>';

        echo '<button data-ii="'.$ii.'" data-nt="'.$nt.'" class="nxs_add_term_compare">'. __('Add More', 'social-networks-auto-poster-facebook-twitter-g') .'...</button>';
        echo '<input type="hidden" id="nxs_count_term_'.$nt.$ii.'" name="'.self::makeInputName('nxs_count_term_compares', $nt, $ii).'" value="'. $count_compares .'">';

        echo "</div>";
    }

    public static function print_lang_section($current_post, $metaSettings, $nt='', $ii='')
    {
        $isVis = !empty($metaSettings['nxs_langs']);
        $ntN = $nt.$ii;
        $langs = array();
        // ## Language
        echo '<h4 onclick="jQuery(\'#nxs_sec_lang'.$ntN.'\').toggle();"; style="cursor:pointer; background-image: url(\''.NXS_PLURL.'img/icons/user24.png\');background-repeat: no-repeat; padding-top: 2px; padding-left: 28px; height:24px;">'.
          __('Language', 'social-networks-auto-poster-facebook-twitter-g') .'&nbsp;[Beta]&nbsp;&gt;&gt; </h4><div id="nxs_sec_lang'.$ntN.'" class="nxsLftPad" style="display:'.($isVis?'block':'none').';">';

        //## PolyLang
        if (function_exists('pll_languages_list')) {
            $args = array('fields'=>'name');
            $l1 = pll_languages_list();
            $l2 = pll_languages_list($args);
            foreach ($l1 as $jj=>$val) {
                $langs[$val] = $l2[$jj];
            }
        }
        //## WPML
        elseif (function_exists('icl_get_languages')) {
            $l = icl_get_languages('skip_missing=0&link_empty_to=str');
            foreach ($l as $lc=>$lObj) {
                $langs[$lc] = !empty($lObj['translated_name'])?$lObj['translated_name']:$lObj['native_name'];
            }
        }
        //## qTranslateX
        elseif (function_exists('qtrans_getSortedLanguages')) {
            $l = qtrans_getSortedLanguages();
            foreach ($l as $lc=>$lObj) {
                $langs[$lc] = $lObj['translated_name'];
            }
        }  //prr($langs);

        if (!empty($langs)) {
            echo '<div><label class="field_title" for="'. 'nxs_langs">'. __('Language', 'social-networks-auto-poster-facebook-twitter-g') . ':</label>&nbsp;&nbsp;<span class="description">'. __('Language') .'</span><br/>';
            self::print_select((!empty($current_post->ID))?$current_post->ID:0, $langs, 'nxs_langs', !empty($metaSettings['nxs_langs'])?$metaSettings['nxs_langs']:'', true, true, self::makeInputName('nxs_langs', $nt, $ii));
            echo '</div>';
        }
        echo "</div>";
    }


    public static function printSimpleSelect($sID, $sName, $value, $optnsList, $sClass = 'nxsSelIt', $txnType='')
    {
        $ph = __('Please select from the list...', 'social-networks-auto-poster-facebook-twitter-g'); ?> <select name="<?php echo $sName; ?>" id="<?php echo $sID; ?>" class="<?php echo $sClass; ?>" data-type="<?php echo $txnType; ?>" placeholder="<?php echo $ph; ?>">
      <?php foreach ($optnsList as $key => $optionName) {
            $selected = $key==$value ? 'selected="selected"' : '';
            echo '<option value="'. esc_attr($key) .'" '. $selected .'>'. esc_attr($optionName) .'</option>';
        } ?>
      </select><?php
    }

    public static function printSelect($sID, $sName, $isMultiple, $optnsList, $values, $postID, $useKeyAsValue = false, $sClass = 'nxsSelIt', $txnType='')
    {
        $ph = __('Please select from the list...', 'social-networks-auto-poster-facebook-twitter-g'); ?> <select name="<?php echo(($isMultiple ? $sName .'[]' : $sName)); ?>" id="<?php echo $sID; ?>" <?php echo($isMultiple ? 'multiple="multiple"' : ''); ?> class="<?php echo $sClass; ?>" data-type="<?php echo $txnType; ?>" placeholder="<?php echo $ph; ?>">
      <?php
        foreach ($optnsList as $key => $optionName) {
            $value = $useKeyAsValue ? $key : $optionName;
            $selected = self::search_value_in_filter_meta($postID, $sName, $value, $values) ? 'selected="selected"' : '';
            echo '<option value="'. esc_attr($value) .'" '. $selected .'>'. esc_attr($optionName) .'</option>';
        } ?></select><?php
    }
    public static function search_value_in_filter_meta($post_id, $meta_name, $needle, $meta_value='')
    {
        if (!empty($meta_value) && !is_array($meta_value) && $needle==$meta_value) {
            return true;
        }
        if (!empty($post_id) && (empty($meta_value)||(!is_array($meta_value)))) {
            $meta_value = get_post_meta($post_id, $meta_name, true);
        }
        // if( is_array( $meta_value ) ) return in_array( (string)$needle, $meta_value, true ); //>?? WHY use strict and (string)???
        if (is_array($meta_value)) {
            return in_array($needle, $meta_value);
        }
        return $meta_value == $needle ? true : false;
    }


    public static function print_select($post_id, $values, $selID_meta_name, $currVal='', $print_key = false, $multiple = true, $name='', $class = 'nxsSelIt', $txnType='')
    {
        $ph = __('Please select from the list...', 'social-networks-auto-poster-facebook-twitter-g');
        if (empty($name)) {
            $name = $selID_meta_name;
        } ?> <select name="<?php echo(($multiple ? $name .'[]' : $name)); ?>" id="<?php echo $selID_meta_name; ?>" <?php echo($multiple ? 'multiple="multiple"' : ''); ?> class="<?php echo $class; ?>" data-type="<?php echo $txnType; ?>" placeholder="<?php echo $ph; ?>">

      <?php // prr($values, "KKKK");
        foreach ($values as $key => $option_item) {
            $value = $print_key ? $key : $option_item; // prr($selID_meta_name); prr($value); prr($currVal);
            $selected = self::search_value_in_filter_meta($post_id, $selID_meta_name, $value, $currVal) ? 'selected="selected"' : '';
            echo '<option value="'. esc_attr($value) .'" '. $selected .'>'. esc_attr($option_item) .'</option>';
        } ?></select><?php
    }

    //## Operations
    public static function search_post_by_id($post_id, $field = 'post_title')
    {
        foreach (self::$posts as $key => $value) {
            if (isset($value->ID) && $value->ID == $post_id) {
                return self::$posts[$key]->$field;
            }
        }
        return null;
    }
    public static function sanitize_data($data)
    {
        if (is_array($data)) {
            return  array_map(array( __CLASS__, 'sanitize_data' ), $data);
        } else {
            return esc_attr(strip_tags($data));
        }
    }
    public static function save_meta($post_id, $key, $data)
    { //echo "<br/> = ".$post_id." ~ ".$key; prr($data);
        if (is_array($data)) {
            $sanitized_data = array_map(array( __CLASS__, 'sanitize_data' ), $data);
        } else {
            $sanitized_data = self::sanitize_data($data);
        }
        //   echo "-= 4 =-";  prr($sanitized_data);
        update_post_meta($post_id, $key, $sanitized_data);
    }
}

function nxs_get_normalize_parameter($param)
{
    // return mb_substr( $param, mb_strpos( $param, '||' ) + 2 );
    return $param;
}

function nxs_removeAllWPQueryFilters()
{
    remove_all_filters('posts_orderby');
    remove_all_filters('posts_where');
    remove_all_filters('posts_fields');
    remove_all_filters('posts_clauses');
    remove_all_filters('posts_distinct');
    remove_all_filters('posts_groupby');
    remove_all_filters('posts_join');
    remove_all_filters('post_limits');
}

//## Filters
if (!function_exists("nxs_snapCheckFilters")) {
    function nxs_snapCheckFilters($options, $postObj)
    {
        $postID = $postObj->ID; //  prr($options, 'FLT1');
  if (!empty($options['fltrsOn']) && !empty($options['fltrs']) && empty($options['fltrAfter'])) {
      $options['fltrs']['nxs_postID'] = $postID;
      $options['fltrs']['fullreturn']='1'; //echo "|Pre FLT 2|";
    nxs_removeAllWPQueryFilters();
      add_filter('pre_get_posts', 'nxs_noSing');
      $pfidRet = get_posts_ids_by_filter($options['fltrs']); /* prr($pfidRet); */ $pfid = $pfidRet['p'];
      remove_filter('pre_get_posts', 'nxs_noSing'); // echo "|W|"; prr($pfidRet); prr($pfid);
    if (empty($pfid) || empty($pfid[0]) || $pfid[0]!=$postID) {
        $msg = nxsAnalyzePostFilters($options['fltrs'], $postObj);
        $postLogRec = "ID: ".$postObj->ID." | ".$postObj->post_title." (".$postObj->post_name.") | Author(ID): ".$postObj->post_author." |Status: ".$postObj->post_status." | Format ".$postObj->filter." | Post Type: ".$postObj->post_type." | <br/>".$msg;
        if (empty($msg)) {
            return 'Filters Block';
        }
        //return "\r\n<br/>".'| Args: '.print_r($pfidRet['a'], true).' '."\r\n<br/>".'| Query: '.$pfidRet['q']."\r\n<br/>".'| Post Filters: '.$msg;
        return $msg;
    }
  }
        return false;
    }
}
//## Analyze Filters
if (!function_exists("nxsAnalyzePostFilters")) {
    function nxsAnalyzePostFilters($filter, $post='')
    {
        $out = '';
        if (!empty($post) && !is_object($post)) {
            $post = get_post($post);
        } //prr($post); prr($filter);
        //## Cats
        if (!empty($filter['nxs_cats_names'])) {
            $fltCats = '';
            $postCats = '';
            foreach ($filter['nxs_cats_names'] as $cctts) {
                $cInfo = get_term($cctts, 'category');
                $fltCats .= $cInfo->name.'|';
            }
            if (!empty($post)) {
                $gg = wp_get_object_terms($post->ID, 'category');
                foreach ($gg as $g) {
                    $postCats .= $g->name.'|';
                }
            }
            if (empty($post) || !empty($postCats) || (!empty($post) && empty($filter['nxs_ie_cats_names']))) {
                $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Categories ('.(empty($filter['nxs_ie_cats_names'])?'Autopost Only':'Excluded').'): '.substr($fltCats, 0, -1).(!empty($postCats)?' | Post Categories: '.$postCats:'');
            }
        }
        //## Tags
        if (!empty($filter['nxs_tags_names'])) {
            $fltT = '';
            $postT = '';
            foreach ($filter['nxs_tags_names'] as $cctts) {
                $cInfo = get_term($cctts, 'post_tag');
                $fltT .= $cInfo->name.'|';
            }
            if (!empty($post)) {
                $gg = wp_get_object_terms($post->ID, 'post_tag');
                foreach ($gg as $g) {
                    $postT .= $g->name.'|';
                }
            }
            if (empty($post) || !empty($postT) || (!empty($post) && empty($filter['nxs_ie_tags_names']))) {
                $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Tags ('.(empty($filter['nxs_ie_tags_names'])?'Autopost Only':'Excluded').'): '.substr($fltT, 0, -1).(!empty($postT)?' | Post Tags: '.$postT:'');
            }
        }
        //## Type
        if (!empty($filter['nxs_post_type'])) {
            $fltT = '';
            foreach ($filter['nxs_post_type'] as $cInfo) {
                $fltT .= $cInfo.'|';
            }
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Post Type ('.(empty($filter['nxs_ie_posttypes'])?'Autopost Only':'Excluded').'): '.substr($fltT, 0, -1).(!empty($post->post_type)?' | Post Type: '.$post->post_type:'');
        }
        //## Format
        if (!empty($filter['nxs_post_formats'])) {
            $fltT = '';
            foreach ($filter['nxs_post_formats'] as $cInfo) {
                $fltT .= $cInfo.'|';
            }
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Post Format (Autopost Only): '.$fltT.(!empty($post->ID)?' | Post Format: '.get_post_format_string(get_post_format($post->ID)):'');
        }
        //## Author
        if (!empty($filter['nxs_user_names'])) {
            $fltT = '';
            $author = '';
            if (!empty($post)) {
                $author = get_user_by('id', $post->post_author);
                $author = $author->user_login."(".$author->user_nicename.")";
            }
            foreach ($filter['nxs_user_names'] as $cctts) {
                $cInfo = get_user_by('id', $cctts);
                $fltT .= $cInfo->user_login."(".$cInfo->user_nicename.")";
            }
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Users (Autopost Only): '.$fltT .(!empty($author)?' | Post Author: '.$author:'');
        }
        //## Language
        if (!empty($filter['nxs_langs'])) {
            $fltT = '';
            $lang = '';
            if (!empty($post)) {
                //## PolyLang
                if (function_exists("pll_get_post_language")) {
                    $pLang = pll_get_post_language($post->ID);
                }
                //## WPML
                if (function_exists("wpml_get_language_information")) {
                    $pLang = wpml_get_language_information($post->ID);
                    $pLang = $pLang['locale'];
                }


                //$author = get_user_by('id', $post->post_author); $author = $author->user_login."(".$author->user_nicename.")";
            }
            foreach ($filter['nxs_langs'] as $cctts) {
                $fltT .= $cctts.' | ';
            }
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Language (Autopost Only): '.$fltT .(!empty($pLang)?' | Post Language: '.$pLang:'');
        }

        //## Post IDS
        if (!empty($filter['nxs_post_ids'])) {
            $fltT = '';
            foreach ($filter['nxs_post_ids'] as $cctts) {
                $cInfo = get_post($cctts);
                $fltT .= $cInfo->post_title." [ID:".$cInfo->ID."] | ";
            }
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Posts (Autopost Only): '.substr($fltT, 0, -2);
            ;
        }
        //## Search
        if (!empty($filter['nxs_search_keywords'])) {
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Search (Autopost Only): '.$filter['nxs_search_keywords'];
        }
        //## Time Frame
        if (!empty($filter['nxs_starting_period']) || !empty($filter['nxs_end_period'])) {
            $count_compares =  (int)$filter['nxs_count_date_periods'];
            for ($i = 1; $i <= $count_compares; $i++) {
                $postfix = $i == 1 ? '' : '_'. $i;
                if (!empty($filter['nxs_starting_period'.$postfix]) && !empty($filter['nxs_end_period'.$postfix])) {
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Time (Autopost Only): Posts from: '.$filter['nxs_starting_period'.$postfix].' to  '.$filter['nxs_end_period'.$postfix];
                } elseif (!empty($filter['nxs_starting_period'.$postfix])) {
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Time (Autopost Only): Posts from: '.$filter['nxs_starting_period'.$postfix];
                } elseif (!empty($filter['nxs_end_period'.$postfix])) {
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Time (Autopost Only): Posts Until: '.$filter['nxs_end_period'.$postfix];
                }
            }
        }
        //## ABS Time Frame
        if (!empty($filter['absPeriods'])) {
            $count_compares =  (int)$filter['nxs_count_abs_periods'];
            for ($i = 1; $i <= $count_compares; $i++) {
                $postfix = $i == 1 ? '' : '_'. $i;
                $indx = $i-1;
                if (empty($filter['absPeriods'][$indx])) {
                    continue;
                }
                $arr = $filter['absPeriods'][$indx];

                if (!empty($arr['start']) && !empty($arr['end'])) {
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: (Autopost Only): Posts newer than '.$arr['start'].' '.$arr['typeStart'].' and older than '.$arr['end'].' '.$arr['typeEnd'];
                } elseif (!empty($arr['start'])) {
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: (Autopost Only): Posts newer than '.$arr['start'].' '.$arr['typeStart'];
                } elseif (!empty($arr['end'])) {
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: (Autopost Only): Posts older than '.$arr['end'].' '.$arr['typeEnd'];
                }
            }
        }

        //## Meta
        if (!empty($filter['nxs_count_meta_compares'])) {
            $count_compares =  (int)$filter['nxs_count_meta_compares'];
            for ($i = 0; $i < $count_compares; $i++) {
                if (!empty($filter['post_meta'][$i]['key'])) {
                    if (!empty($post)) {
                        $val = get_post_meta($post->ID, $filter['post_meta'][$i]['key']);
                        if (empty($val)) {
                            $val = 'NULL';
                        }
                    } else {
                        $val = '';
                    }
                    if ($filter['post_meta'][$i]['key']!='snap_isAutoPosted' && stripos($filter['post_meta'][$i]['key'], 'snap_isRpstd')===false) {
                        $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Custom Field: '.$filter['post_meta'][$i]['key'].' '.$filter['post_meta'][$i]['operator'].' '.$filter['post_meta'][$i]['value'][0].(!empty($val)?' | Actual: '.print_r($val, true):'');
                    }
                }
            }
        }
        //## Taxonomies
        if (!empty($filter['nxs_term_names'])) {
            $count_compares =  (int)$filter['nxs_count_term_compares'];
            for ($i = 1; $i <= $count_compares; $i++) {
                $postfix = $i == 1 ? '' : '_'. $i;
                if (!empty($filter["nxs_tax_names$postfix"])) {
                    if (!empty($post)) {
                        $gg = wp_get_object_terms($post->ID, $filter["nxs_tax_names$postfix"]);
                    } else {
                        $gg = '';
                    }
                    $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Custom Taxonomy: '. $filter["nxs_tax_names$postfix"].' '.$filter["nxs_term_operator$postfix"].' '.print_r($filter["nxs_term_names$postfix"], true).(!empty($gg)?' | Actual: '.print_r($gg, true):'');
                }
            }
        }
        //if (!empty($filter['nxs_term_names'])) $gg = wp_get_object_terms( $post->ID, $filter['tax']);
        return substr($out, 5);
    }
}

if (!function_exists("nxs_check_dates")) {
    function nxs_check_dates($date)
    {
        if (strtotime($date)==false && strpos($date, '/')!==false) {
            return (str_replace('/', '-', $date));
        } else {
            return $date;
        }
    }
}

function get_posts_ids_by_filter($filter)
{
    $args = array('fields' => 'ids'); //prr($filter);
    //## Do info first, we temporary cange $filters to process.....
    $outInfo = !empty($filter['fullreturn'])?nxsAnalyzePostFilters($filter):'';
    //## Set args
    if (!empty($filter['nxs_postID'])) {
        $args['p'] = $filter['nxs_postID'];
    }
    if (!empty($filter['nxs_user_names'])) {
        $args['author__in'] = $filter['nxs_user_names'];
    }
    if (!empty($filter['nxs_langs'])) {
        $args['lang'] = $filter['nxs_langs'];
    }
    if (!empty($filter['nxs_cats_names'])) {
        if (empty($filter['nxs_ie_cats_names'])) {
            $args['category__in'] = $filter['nxs_cats_names'];
        } else {
            $args['category__not_in'] = $filter['nxs_cats_names'];
        }
    }
    if (!empty($filter['nxs_tags_names'])) {
        if (empty($filter['nxs_ie_tags_names'])) {
            $args['tag__in'] = $filter['nxs_tags_names'];
        } else {
            $args['tag__not_in'] = $filter['nxs_tags_names'];
        }
    }
    if (!empty($filter['nxs_name_page'])) {
        $args['page_id'] = $filter['nxs_name_page'];
    }
    if (!empty($filter['nxs_name_post'])) {
        $args['post__in'] = $filter['nxs_name_post'];
    }

    if (!empty($filter['nxs_post_ids'])) {
        $args['post__in'] = $filter['nxs_post_ids'];
    }


    if (!empty($filter['nxs_name_parent'])) {
        $args['post_parent__in'] = $filter['nxs_name_parent'];
    }
    if (empty($filter['nxs_post_type'])) {
        $filter['nxs_post_type'][] = 'NiHuyaSebeType';
        $filter['nxs_ie_posttypes'] = 1;
    }
    if (!empty($filter['nxs_ie_posttypes'])) {
        nxs_Filters::init(true);
        $filter['nxs_post_type'] = array_diff(nxs_Filters::$posts_types, $filter['nxs_post_type']);
    }
    if (!empty($filter['nxs_post_type'])) {
        $args['post_type'] = $filter['nxs_post_type'];
    }
    if (!empty($filter['nxs_search_keywords'])) {
        $args['s'] = $filter['nxs_search_keywords'];
    }
    if (!empty($filter['nxs_sticky_post'])) {
        $args['ignore_sticky_posts'] = $filter['nxs_sticky_post'] == 'no' ? true : false;
    }
    //  if( !empty( $filter['nxs_tags_names'] ) )         $args['second'] = $filter['nxs_second'];   -  ################### CHto ETO??????????
    //## Custom Taxonomies & Custom fields
    if (function_exists('nxs_doSMAS7')) {
        $args = nxs_doSMAS7($args, $filter);
    } else {
        if (!empty($filter['post_meta']) && is_array($filter['post_meta'])) {
            $meta_compares = array();
            foreach ($filter['post_meta'] as $pm) {
                if ($pm['key']=='snap_isAutoPosted' || stripos($pm['key'], 'snap_isRpstd')!==false) {
                    $new_compare = array();
                    $new_compare['key'] = $pm['key'];
                    $new_compare['compare'] = 'NOT EXISTS';
                    $new_compare['value'] = '';
                    $meta_compares[] = $new_compare;
                }
            }
            if (!empty($meta_compares)) {
                $meta_compares['relation'] = 'AND';
            }
            $args['meta_query'] = $meta_compares;
        }
    }
    //## Post formats
    if (!empty($filter['nxs_post_formats'])) {
        $post_formats  = $filter['nxs_post_formats'];
        $formats_query = array( 'taxonomy' => 'post_format', 'terms' => $post_formats, 'field' => 'slug' );
        $args['tax_query'][]   = $formats_query;
        if (in_array('standard', $post_formats)) {
            $reg_formats = get_theme_support('post-formats');
            if (is_array($reg_formats) && is_array($reg_formats[0])) {
                $reg_formats = $reg_formats[0];
            }
            if (is_array($reg_formats)) {
                $formats = array();
                foreach ($reg_formats as $format) {
                    $formats[] = 'post-format-'. $format;
                }
                $args['tax_query'][] = array( 'taxonomy' => 'post_format', 'terms' => $formats, 'field' => 'slug', 'operator' => 'NOT IN' );
                $args['tax_query']['relation'] = 'OR';
            }
        }
    }


    if (isset($filter['nxs_count_date_periods']) && (!empty($filter['nxs_starting_period']) || !empty($filter['nxs_end_period']))) {
        $date_compares = array();
        $count_compares = $filter['nxs_count_date_periods'];
        $date_compares['relation'] = 'OR';
        for ($i = 1; $i <= $count_compares; $i++) {
            $postfix = $i > 1 ? '_'. $i : '';
            $new_compare = array();
            if (!empty($filter["nxs_starting_period$postfix"])) {
                $new_compare['after'] = nxs_check_dates($filter["nxs_starting_period$postfix"]);
            }
            if (!empty($filter["nxs_end_period$postfix"])) {
                $new_compare['before'] = nxs_check_dates($filter["nxs_end_period$postfix"]);
            }
            $new_compare['inclusive'] = (!empty($filter["nxs_inclusive$postfix"]) && $filter["nxs_inclusive$postfix"] == 'on') ? true : false ;
            if (!empty($new_compare)) {
                $date_compares[] = $new_compare;
            }
        }
        $args['date_query'] = $date_compares;
    }
    /*
    if( ( !empty( $filter['nxs_starting_abs_period'] ) && !empty( $filter['nxs_types_starting_abs_period'] ) ) || ( !empty( $filter['nxs_end_abs_period'] ) && !empty( $filter['nxs_types_end_abs_period'] ) ) ) {
        $abs_date_compares = array(); $abs_count_compares = $filter['nxs_count_date_abs_periods'];
        for( $i = 1; $i <= $abs_count_compares; $i++ ) { $postfix = $i > 1 ? '_'. $i : ''; $new_abs_compare = array();
            if( !empty( $filter["nxs_starting_abs_period$postfix"] ) && !empty( $filter["nxs_types_starting_abs_period$postfix"] ) ) {
                $new_abs_compare['after'] = intval( $filter["nxs_starting_abs_period$postfix"] ) .' '. $filter["nxs_types_starting_abs_period$postfix"] .' ago';
            }
            if( !empty( $filter["nxs_end_abs_period$postfix"] ) && !empty( $filter["nxs_types_end_abs_period$postfix"] ) ) {
                $new_abs_compare['before'] = intval( $filter["nxs_end_abs_period$postfix"] ) .' '. $filter["nxs_types_end_abs_period$postfix"] .' ago';
            }
            if( !empty( $new_abs_compare ) ) $abs_date_compares[] = $new_abs_compare;
        }
        if( !empty( $abs_date_compares ) ) {
          if( empty( $args['date_query'] ) ) $args['date_query']   = $abs_date_compares; else  foreach( $abs_date_compares as $abs_date_compare ) $args['date_query'][] = $abs_date_compare;
        }
    }
    */
    if (!empty($filter['absPeriods'])) {
        $abs_date_compares = array();
        $abs_count_compares = $filter['nxs_count_abs_periods'];
        for ($i = 1; $i <= $abs_count_compares; $i++) {
            $postfix = $i > 1 ? '_'. $i : '';
            $new_abs_compare = array();
            $indx = $i-1;
            if (empty($filter['absPeriods'][$indx])) {
                continue;
            }
            $arr = $filter['absPeriods'][$indx];
            if (!empty($arr["start"]) && !empty($arr["typeStart"])) {
                $new_abs_compare['after'] = intval($arr["start"]) .' '. $arr["typeStart"] .' ago';
            }
            if (!empty($arr["end"]) && !empty($arr["typeEnd"])) {
                $new_abs_compare['before'] = intval($arr["end"]) .' '. $arr["typeEnd"] .' ago';
            }
            if (!empty($new_abs_compare)) {
                $abs_date_compares[] = $new_abs_compare;
            }
        }
        if (!empty($abs_date_compares)) {
            if (empty($args['date_query'])) {
                $args['date_query']   = $abs_date_compares;
            } else {
                foreach ($abs_date_compares as $abs_date_compare) {
                    $args['date_query'][] = $abs_date_compare;
                }
            }
        }
    }
    if (!empty($filter['nxs_permission'])) {
        $args['perm'] = $filter['nxs_permission'];
    }
    //$args['p'] = array('ID'=>'1200', 'compare'=>'<');
    $args['numberposts'] = -1;
    $args['post_status'] = array('publish');
    //$args['post_status'] = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash');
    if (!empty($filter['nxs_post_status'])) {
        $post_status = $filter['nxs_post_status'];
    }
    //## SORT
    if (!empty($filter['orderby'])) {
        $args['orderby'] = $filter['orderby'];
    }
    if (!empty($filter['order'])) {
        $args['order'] = $filter['order'];
    }
    if (!empty($filter['posts_per_page'])) {
        $args['posts_per_page'] = $filter['posts_per_page'];
    }

    //## WPML
    if (function_exists('wpml_get_language_information') && !empty($args['lang'])) {
        $args['posts_per_page'] = -1;
    }
    //############### GET Posts
    $args['suppress_filters'] = true;
    $postsRet = nxs_get_posts($args);
    $post_ids = $postsRet['p'];
    $qu = $postsRet['q'];
    // $la = array('msg'=>'FLT Res X', 'extInfo'=>print_r($filter, true)." | ".print_r($post_ids, true));   nxsLogIt($la);

    //## WPML
    if (function_exists('wpml_get_language_information') && !empty($args['lang'])) {
        $pii = array();
        foreach ($post_ids as $pid) {
            $clang = apply_filters('wpml_post_language_details', null, $pid);
            if (in_array($clang['language_code'], $filter['nxs_langs'])) {
                $pii[] = $pid;
            }
        }
        $post_ids = $pii;
    }
    // $la = array('msg'=>'FLT Res', 'extInfo'=>print_r($filter, true)." | ".print_r($post_ids, true));   nxsLogIt($la);
    //prr($qu);  prr($post_ids);  prr($args);  prr($outInfo); prr($filter);
    if (!empty($filter['fullreturn'])) {
        return array('p'=>$post_ids, 'q'=>$qu, 'a'=>$args, 'i'=>$outInfo, 'f'=>$filter);
    } else {
        return $post_ids;
    }
}


//## WP Function but returns the Query too (for log).
if (!function_exists("nxs_get_posts")) {
    function nxs_get_posts($args = null)
    {
        $defaults = array( 'numberposts' => 5, 'offset' => 0, 'category' => 0, 'orderby' => 'date', 'order' => 'DESC',
      'include' => array(), 'exclude' => array(), 'meta_key' => '', 'meta_value' =>'', 'post_type' => 'post', 'suppress_filters' => true
    );
        $r = wp_parse_args($args, $defaults);
        if (empty($r['post_status'])) {
            $r['post_status'] = ('attachment' == $r['post_type']) ? 'inherit' : 'publish';
        }
        if (! empty($r['numberposts']) && empty($r['posts_per_page'])) {
            $r['posts_per_page'] = $r['numberposts'];
        }
        if (! empty($r['category'])) {
            $r['cat'] = $r['category'];
        }
        if (! empty($r['include'])) {
            $incposts = wp_parse_id_list($r['include']);
            $r['posts_per_page'] = count($incposts);
            $r['post__in'] = $incposts;
        } elseif (! empty($r['exclude'])) {
            $r['post__not_in'] = wp_parse_id_list($r['exclude']);
        }
        $r['ignore_sticky_posts'] = true;
        $r['no_found_rows'] = true;
        $get_posts = new WP_Query;
        $posts = $get_posts->query($r);
        $qu = $get_posts->request;
        return array('p'=>$posts, 'q'=>$qu);
    }
}
?>
