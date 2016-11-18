<?php
$access_token = '6t9nTGUM4MSBtfVGcj3hTogXFEr7/Uc1ZrSnJJpuzHsegwHbA0Ja0hbePr2xBz86R54nNKFd/EWYtYxwHrhJsBSe6TlG88XT7fHfhRCn57F1Rj+XwNVhq5jYmqcIflZjqTU6+jKwn4GhZZ/CNIRwwQdB04t89/1O/w1cDnyilFU=ISSUE';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
$to = $events->{"result"}[0]->{"content"}->{"from"}; //หาผู้ส่ง 
$text = $events->{"result"}[0]->{"content"}->{"text"}; //หาข้อความที่โพสมา 
$text_ex = explode(':', $text); 


// Validate parsed JSON data
if (!is_null($events['events'])) {
    // Loop through each event
    foreach ($events['events'] as $event) {
        // Reply only when message sent is in 'text' format
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            // Get text sent
            $text = $event['message']['text'];
            // Get replyToken
            $replyToken = $event['replyToken'];

            // Build message to reply back
            $messages = [
                'type' => 'text',
                'text' => $text
            ];


 if ($text_ex[0] == "อยากรู้"){ 
     $ch1 = curl_init(); 
         curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); 
         curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
         curl_setopt($ch1, CURLOPT_URL, 'https://th.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$text_ex[1]); 

         $result1 = curl_exec($ch1); 
         curl_close($ch1); 
         $obj = json_decode($result1, true);

          foreach($obj['query']['pages'] as $key => $val)
          {
           $result_text = $val['extract']; 
          } 


          if(empty($result_text)){//ถ้าไม่พบให้หาจาก en
           $ch1 = curl_init(); 
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch1, CURLOPT_URL, 'https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$text_ex[1]); 

            $result1 = curl_exec($ch1); 
            curl_close($ch1); 
            $obj = json_decode($result1, true); 

            foreach($obj['query']['pages'] as $key => $val)
                { 
                    $result_text = $val['extract']; 
                }
                 } 

            if(empty($result_text)){//หาจาก en ไม่พบก็บอกว่า ไม่พบข้อมูล ตอบกลับไป 
                $result_text = 'ไม่พบข้อมูล'; } 
            

            // Make a POST Request to Messaging API to reply to sender
            $url = 'https://api.line.me/v2/bot/message/reply';
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$result_text],
            ];
            $post = json_encode($data);
            $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            echo $result . "\r\n";
        }}
    }
}
echo "OK";