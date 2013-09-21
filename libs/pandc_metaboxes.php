<?php

class PAndCMetaboxes {

  function metaboxes_add(){
      global $pandc_metaboxes;

      foreach($pandc_metaboxes as $post_type => $box) {
          $i = 0;
          foreach($box as $key => $value){
              $args = array('box_num' => $i);
              add_meta_box($value['id'], $value['title'],array( &$this, 'format_custom_fields'), $post_type, $value['context'], $value['priority'], $args);  
              $i++;
          }
      }
  }

  //Format meta boxes
  function format_custom_fields($post, $args) {
    global $pandc_metaboxes, $post;
   
    // Use nonce for verification
    echo '<input type="hidden" name="plib_pc_metaboxes_nonce" value="' , wp_create_nonce(basename(__FILE__)) , '" />';
   
    echo '<table class="form-table">';
    $x = $pandc_metaboxes[$post->post_type];
    $box = $x[$args['args']['box_num']];
    foreach ($box['fields'] as $field){         
      // get current post meta data
      $meta = get_post_meta($post->ID, $field['id'], true);

      echo '<tr>'.
              '<th style="width:20%"><label for="'. $field['id'] .'">'. $field['name']. '</label></th>'.
              '<td>';
          switch ($field['type']) {
              case 'text':
                  echo '<input type="text" name="'. $field['id']. '" id="'. $field['id'] .'" value="'.    ($meta ? $meta : $field['default']) . '" size="30" style="width:97%" />'. '<br />'. $field['desc'];
                  break;
              case 'textarea':
                  echo '<textarea name="'. $field['id']. '" id="'. $field['id']. '" cols="60" rows="4" style="width:97%">'. ($meta ? $meta : $field['default']) . '</textarea>'. '<br />'. $field['desc'];
                  break;
              case 'select':
                  echo '<select name="'. $field['id'] . '" id="'. $field['id'] . '">';
                  foreach ($field['options'] as $option) {
                      echo '<option '. ( $meta == $option ? ' selected="selected"' : '' ) . '>'. $option . '</option>';
                  }
                  echo '</select>';
                  break;
              case 'radio':
                  foreach ($field['options'] as $option) {
                      echo '<input type="radio" name="' . $field['id'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . ' /> ' . $option['name'] . "<br/>";
                  }
                  break;
              case 'checkbox':
                  echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '"' . ( $meta ? ' checked="checked"' : '' ) . ' />';
                  break;
              case 'file':
                  if ($meta ){
                      echo '<img src="' . $meta .'"/>';
                      echo '<input type="file" id="'. $field['id'] . '" name="'. $field['id'] . '" value="" size="25">';
                  }else {
                      echo '<input type="file" id="'. $field['id'] . '" name="'. $field['id'] . '" value="" size="25">';
                  }
          }
          echo '<td>'.'</tr>';
      }

      echo '</table>';
  }


  // Save data from meta box
  function metaboxes_update($post_id) {
      global $pandc_metaboxes, $post;
      
      //Verify nonce
      if (!wp_verify_nonce($_POST['plib_pc_metaboxes_nonce'], basename(__FILE__))) {
          return $post_id;
      }
   
      //Check autosave
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return $post_id;
      }
   
      //Check permissions
      if ('page' == $_POST['post_type']) {
          if (!current_user_can('edit_page', $post_id)) {
              return $post_id;
          }
      } elseif (!current_user_can('edit_post', $post_id)) {
          return $post_id;
      }

      foreach($pandc_metaboxes[$post->post_type] as $type){
           foreach($type['fields'] as $field) {
              $old = get_post_meta($post_id, $field['id'], true);
              $new = $_POST[$field['id']];
              if ( 'file' == $field['type']) {
                  if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
                  $allowed_file_types = array('jpg' =>'image/jpg','jpeg' =>'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');
                  $overrides = array('test_form' => false, 'mimes' => $allowed_file_types);

                  $file = $field['id'];
                  $upload = wp_handle_upload( $_FILES[$file], $overrides );
                  update_post_meta( $post_id, $field['id'], $upload );
              } else {
                  if ($new && $new != $old) {
                  update_post_meta($post_id, $field['id'], $new);
                  } elseif ('' == $new && $old) {
                      delete_post_meta($post_id, $field['id'], $old);
                  }
              }
          }
      }
  }

  function update_custom_meta_data( $id, $data_key, $is_file = false ) {

      if( $is_file && ! empty( $_FILES ) ) {

          $upload = wp_handle_upload( $_FILES[$data_key], array( 'test_form' => false ) );

          if( isset( $upload['error'] ) && '0' != $upload['error'] ) {

              wp_die( 'There was an error uploading your file. ' );

          } else {

              update_post_meta( $id, $data_key, $upload );

          } // end if/else

      } // end if/else

  } // end update_data

} // end Metaboxes

?>