<?php
	include_once("secret.php");

	function createCampaign($url, $message) {
		global $server;
		global $api_key;

		// Create campaign
		$data = [
			"type" => "regular",
			"recipients" => [
				"list_id" => "58e7ee09d2",
				// "segment_opts" => [ // comment section to send to everyone
				// 	"saved_segment_id" => 1393,
				// ],
			],
			"settings" => [
				"subject_line" => "Dog-a-Day: *|DATE:F jS|*",
				"title" => "Dog-a-Day: " . date('F jS'), // add in test to make it clear the purpose
				"from_name" => "Dog-a-Day",
				"reply_to" => "dogaday@alexbeals.com",
				"to_name" => "*|FNAME|*",
			],
		];
		$data_string = json_encode($data);

		$ch = curl_init("https://$server.api.mailchimp.com/3.0/campaigns");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_USERPWD, "anything:$api_key");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($data_string)) );

		$campaign_info = json_decode(curl_exec($ch), true);

		// Fill with proper information
		$data = [
			"template" => [
				"id" => 5557,
				"sections" => [
					"message" => "$message",
					"image" => "<img src='$url' alt='Dog-a-Day' width='480' border='0' class='emailImage'>",
				],
			],
		];
		$data_string = json_encode($data);

		$ch = curl_init("https://$server.api.mailchimp.com/3.0/campaigns/" . $campaign_info["id"] . "/content");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_USERPWD, "anything:$api_key");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($data_string)) );

		$result = curl_exec($ch);

		// Send it out
		$ch = curl_init("https://$server.api.mailchimp.com/3.0/campaigns/" . $campaign_info["id"] . "/actions/send");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_USERPWD, "anything:$api_key");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		// echo "<pre>" . print_r(json_decode($result, true), true) . "</pre>";
	}
?>