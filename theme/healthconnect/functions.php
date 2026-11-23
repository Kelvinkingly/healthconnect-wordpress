<?php
if (!defined('ABSPATH')) { exit; }
add_action('after_setup_theme', function () { add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('automatic-feed-links'); });
add_action('wp_enqueue_scripts', function () { wp_enqueue_style('healthconnect', get_stylesheet_uri(), array(), '1.0.0'); wp_enqueue_script('healthconnect', get_template_directory_uri().'/app.js', array(), '1.0.0', true); });
add_action('init', function () {
    foreach (array('hc_provider'=>'Providers','hc_clinic'=>'Clinics','hc_service'=>'Services') as $type=>$label) {
        register_post_type($type, array('label'=>$label,'public'=>true,'show_in_rest'=>true,'has_archive'=>true,'supports'=>array('title','editor','excerpt','thumbnail'),'menu_icon'=>'dashicons-heart'));
    }
    register_taxonomy('hc_specialty',array('hc_provider','hc_service'),array('label'=>'Specialties','public'=>true,'show_in_rest'=>true));
    register_taxonomy('hc_area',array('hc_clinic','hc_provider'),array('label'=>'Areas','public'=>true,'show_in_rest'=>true));
});
add_action('after_switch_theme',function(){
    if(get_option('hc_seeded_v1')) return;
    $data=array('hc_provider'=>array(array('Dr. Avery Morgan','Family medicine • English and Spanish','Primary care','North district'),array('Dr. Jordan Ellis','Children and family care • English','Pediatrics','Central district'),array('Dr. Casey Rivera','Preventive care • English and Spanish','Primary care','South district')),'hc_clinic'=>array(array('North Health Center','Illustrative community clinic • Weekday hours','','North district'),array('Central Health Center','Illustrative community clinic • Weekday hours','','Central district'),array('South Health Center','Illustrative community clinic • Weekday hours','','South district')),'hc_service'=>array(array('Primary care','Explore general care and wellness information.','Primary care',''),array('Pediatrics','Explore family and child health information.','Pediatrics',''),array('Preventive care','Explore preventive services and wellness information.','Primary care','')));
    foreach($data as $type=>$items){foreach($items as $item){$id=wp_insert_post(array('post_type'=>$type,'post_title'=>$item[0],'post_content'=>$item[1],'post_status'=>'publish'),true);if(is_wp_error($id))continue;if($item[2])wp_set_object_terms($id,$item[2],'hc_specialty');if($item[3])wp_set_object_terms($id,$item[3],'hc_area');}}
    update_option('hc_seeded_v1',1);
});
function hc_text($en,$es){return isset($_GET['lang']) && is_string($_GET['lang']) && $_GET['lang']==='es'?$es:$en;}
function hc_cards($type){$q=new WP_Query(array('post_type'=>$type,'posts_per_page'=>-1,'orderby'=>'title','order'=>'ASC'));if(!$q->have_posts()){echo '<p>'.esc_html(hc_text('No entries yet.','Aún no hay registros.')).'</p>';return;}while($q->have_posts()){$q->the_post();$terms=wp_get_post_terms(get_the_ID(),array('hc_specialty','hc_area'),array('fields'=>'names'));$terms=is_wp_error($terms)?array():$terms;?><article class="card searchable" data-search="<?php echo esc_attr(implode(' ',$terms).' '.get_the_title().' '.wp_strip_all_tags(get_the_content())); ?>"><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p><?php foreach($terms as $term){echo '<span class="badge">'.esc_html($term).'</span> ';}?></article><?php }wp_reset_postdata();}
