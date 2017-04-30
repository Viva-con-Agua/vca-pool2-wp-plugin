<?php
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
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "localhost:9000/test");
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                         
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($data_string))                                                                       
		);

		$result = curl_exec($curl);
		curl_close($curl);

		//echo htmlspecialchars_decode($result, ENT_COMPAT);

		return $result;
	}

	add_action('rest_api_init', function() {
		register_rest_route('templates', '/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => 'getTemplate'
		));
	});
?>