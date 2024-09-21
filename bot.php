<?php  
date_default_timezone_set("UTC");
system('clear');
error_reporting(0); 
$useragent = "Mozilla/5.0 (Linux; Android 11; 2201116SG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.92 Mobile Safari/537.36"; 
$cookie = "_ga=GA1.1.772036795.1671964350;0x369b808887=0x369b808887;_ga_NW9ZPXZGM4=GS1.1.1672110821.3.1.1672110853.0.0.0;PHPSESSID=f3488ago940hi208sd500dvbar;spin_id=156226"; 

// Fungsi untuk mendapatkan data dari URL 
function Get($url, $ua){ 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    curl_setopt($ch, CURLOPT_ENCODING, ""); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $ua); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    $result = curl_exec($ch); 
    curl_close($ch); 
    return $result;  
} 

// Fungsi untuk mengirim notifikasi ke Telegram 
function sendTelegramMessage($chat_id, $message, $bot_token) { 
    $url = "https://api.telegram.org/bot$bot_token/sendMessage"; 
    $data = [ 
        'chat_id' => $chat_id, 
        'text' => $message, 
        'parse_mode' => 'HTML' // Untuk mendukung hyperlink di pesan 
    ]; 
     
    $options = [ 
        'http' => [ 
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n", 
            'method'  => 'POST', 
            'content' => http_build_query($data), 
        ], 
    ]; 
     
    $context  = stream_context_create($options); 
    $result = file_get_contents($url, false, $context); 
     
    if ($result === FALSE) { 
        echo "Error sending message to Telegram."; 
    } 
} 

// URL dan user-agent array 
$url_sold = "https://fragment.com/?sort=ending&filter=sold"; 
$url_listed = "https://fragment.com/?sort=listed&filter=sale"; 
$url_auction = "https://fragment.com/?sort=listed&filter=auction"; 
$ua = ["user-agent: " . $useragent]; 

// Variabel untuk menyimpan username yang terakhir terjual, di-listing, dan di-auction 
$last_sold_uname = ""; 
$last_listed_uname = ""; 
$last_auction_uname = ""; 

// Token dan chat_id Telegram 
$bot_token = "7238716933:AAES9uzR5tgw3tvF9mBVClh4uhtOVA1m_uA"; // Ganti dengan bot token milikmu 
$chat_id = "1787445189"; // Ganti dengan chat_id kamu 
$channel_chat_id = "-1002276312812"; // Ganti dengan chat_id channel milikmu

// Loop tak terbatas untuk memantau setiap 10 detik 
while (true) { 
    $updates = false; 

    // ------------------ Bagian untuk username yang terjual ------------------ 
    $i_sold = Get($url_sold, $ua); 
    $tm_sold = explode('</time></div>', explode('00:00">', $i_sold)[1])[0]; 
    $uname_sold = explode('" class="table-cell">', explode('<a href="/username/', $i_sold)[1])[0]; 
    $prc_sold = explode('</div>', explode('<div class="table-cell-value tm-value icon-before icon-ton">', $i_sold)[1])[0]; 

    if ($uname_sold != $last_sold_uname) { 
        $message = "📢 a username has been Sold 🎉\n\n"."🔗 <a href='https://fragment.com/username/".$uname_sold."'>"."@".$uname_sold."</a>\n\n"."💸Sold: ".$prc_sold." TON 💎\n\n"."⚡Just Now, ".date('H:i:s')." (UTC)\n••••••••••••••••••••••••••••••••••••••••••••\n 📩 Ads placements?\n DM: @yzzztv / @NPC_0xGamers\n\n"; 
        sendTelegramMessage($chat_id, $message, $bot_token); 
        sendTelegramMessage($channel_chat_id, $message, $bot_token); // Kirim ke channel
        $last_sold_uname = $uname_sold; 
        $updates = true; 
    } 

    // ------------------ Bagian untuk username yang di-listing ------------------ 
    $i_listed = Get($url_listed, $ua); 
    $tm_listed = explode('</time></div>', explode('00:00">', $i_listed)[1])[0]; 
    $uname_listed = explode('" class="table-cell">', explode('<a href="/username/', $i_listed)[1])[0]; 
    $prc_listed = explode('</div>', explode('<div class="table-cell-value tm-value icon-before icon-ton">', $i_listed)[1])[0]; 

    if ($uname_listed != $last_listed_uname) { 
        $message = "📢 a username has been Listed ✍🏻\n\n"."🔗 <a href='https://fragment.com/username/".$uname_listed."'>"."@".$uname_listed."</a>\n\n"."🧾Price: ".$prc_listed." TON 💎\n\n"."⚡Just Now, ".date('H:i:s')." (UTC)\n••••••••••••••••••••••••••••••••••••••••••••\n 📩 Ads placements?\n DM: @yzzztv / @NPC_0xGamers\n\n";  
        sendTelegramMessage($chat_id, $message, $bot_token); 
        sendTelegramMessage($channel_chat_id, $message, $bot_token); // Kirim ke channel
        $last_listed_uname = $uname_listed; 
        $updates = true; 
    } 

    // ------------------ Bagian untuk username yang di-auction ------------------ 
    $i_auction = Get($url_auction, $ua); 
    $tm_auction = explode('</time></div>', explode('00:00">', $i_auction)[1])[0]; 
    $uname_auction = explode('" class="table-cell">', explode('<a href="/username/', $i_auction)[1])[0]; 
    $prc_auction = explode('</div>', explode('<div class="table-cell-value tm-value icon-before icon-ton">', $i_auction)[1])[0]; 

    if ($uname_auction != $last_auction_uname) { 
        $message = "📢 a username listed on Auction ⚖️\n\n"."🔗 <a href='https://fragment.com/username/".$uname_auction."'>"."@".$uname_auction."</a>\n\n"."🧾Price: ".$prc_auction." TON 💎 \n\n⚠️End: ".$tm_auction." (UTC)\n••••••••••••••••••••••••••••••••••••••••••••\n 📩 Ads placements?\n DM: @yzzztv / @NPC_0xGamers\n\n"; 
        sendTelegramMessage($chat_id, $message, $bot_token); 
        sendTelegramMessage($channel_chat_id, $message, $bot_token); // Kirim ke channel
        $last_auction_uname = $uname_auction; 
        $updates = true; 
    } 

    // Hanya jika tidak ada perubahan dari ketiga data, tampilkan pesan standby 
    if (!$updates) { 
        echo "No new updates. Standby...\n"; 
    } 

    // Tunggu 10 detik sebelum mengambil data lagi 
    sleep(10); 
} 
?>
