<?php

declare(strict_types=1);

return [

	// chat_room:{$room_id} => {$user_id => $fd}
    'room_key' => 'chat_room:%s', // 得知房間有哪幾個用戶跟 fd

    // fd_map_user_id =>  {$fd => $user_id}
    'fd_user_id_map_key' => 'fd_bind_user_id',  // 得知 fd 對應哪個 user_id

    // fd_room:{$fd} =>   {$room_id => $user_id(沒在用)}
    'fd_room_map_key' => 'fd_in_room:%s',  // 得知fd在哪幾間房間
];
