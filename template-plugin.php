<?php

include_once('simple_html_dom.php');

/**
 * Template Plugin
 *
 * Lorem
 *
 * @author Patte
 * @since 0.1.0.0
 */

/*
Plugin Name: Template Plugin
Description: Send information to dispenser and retrieves the template
Author: Patte
Version: 1.0.0
License: GPLv2
*/
	function getTemplate($data) {
		//ob_get_clean();
		//$data[] = ["microServiceName" => "reservoir", "template" => "simpleTemplate", searchEngineKeywords => ""];
		//$data[] = ["title" => "test"];
		$args = array(
			'p' => $data['id'],
			'post_type' => 'any'
		);
		$loop = new WP_QUERY($args);
		global $post;

		while($loop->have_posts()) : $loop->the_post();
			setup_postdata( $loop->post->title );
			setup_postdata($loop->post->content);
			$data = array('metaData' => array('microServiceName' => 'reservoir', 'template' => 'simpleTemplate', 'searchEngineKeywords' => array()), 'templateData' => array('title' => (string)$post->post_title, 'body' => (string)$post->post_content));
		endwhile; 	
		
		$data_string = json_encode($data);
		/*$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "localhost:9000/test");
		//curl_setopt($curl, CURLOPT_POST, 1);
		//curl_setopt($curl, CURLOPT_HEADER, false);
		//curl_setopt($curl, CURLOPT_ENCODING, "");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                         
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($data_string))                                                                       
		);
		//curl_setopt($curl, CURLOPT_VERBOSE, 0);

	    /*$content = curl_exec( $curl );
	    $err     = curl_errno( $curl );
	    $errmsg  = curl_error( $curl );
	    $header  = curl_getinfo( $curl );
	    curl_close( $curl );

	    $header['errno']   = $err;
    	$header['errmsg']  = $errmsg;
    	$header['content'] = $content;

    	return $header;*/

    	/*$result = curl_exec($curl);
    	curl_close($curl);

    	$html = str_get_html($result);

    	echo $html;*/
    	//echo $data_string;
    	$html = new simple_html_dom();

    	$url = "http://localhost:9000/test";
    	$result = wp_remote_post($url, array('headers' => array('Content-Type' => 'application/json; charset=utf8'),
    		'body' => $data_string, 'method' => 'POST'));

    	//echo $result['body'];

 		$html = str_get_html(wp_remote_retrieve_body($result));
    	$html->load(wp_remote_retrieve_body($result));
    	echo $html;

 		//echo $html->save();
    	//echo $result;

    	$response = new WP_REST_Response($html);
    	$response->header('Content-Type', 'text/html; charset=utf-8');
    	$response->set_status(200);

    	return $response;

    	//WPBMap::addAllMappedShortcodes();
    	//$output = apply_filters('the_content', $html->find('body', '1'));
    	//return $output;
	}

	add_action('rest_api_init', function() {
		register_rest_route('templates', '/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => 'getTemplate'
		));
	});
?>