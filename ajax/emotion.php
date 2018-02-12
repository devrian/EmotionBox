<?php

// ===SET UP===
require_once '../init.php';
$DB->setTable('emotions');

//===SET SESSION===
session_start();
$_SESSION['id'] = 1;

// ===CATH PARAMETER===
$action     = $_GET['action'];
$article_id = $_GET['article_id'];

switch ($action) {
    case 'all_data':
        get_all_data($article_id);
        break;
    case 'vote':
        $emotion_id = $_GET['emotion_id'];
        save_emotion($article_id, $emotion_id);
        break;
    
    default:
        # code...
        break;
}

/** 
 * Retrieve all emmotions
 */
function get_all_data($article_id)
{
    global $DB;
    $emotions = $DB->where('article_id', '=', $article_id)->all();
    $total    = count($emotions);
    $happy    = 0; $sad = 0; $angry = 0;


    foreach ($emotions as $emotion) {
        switch ($emotion->emotion_id) {
            case 0: $happy++; break;
            case 1: $sad++; break;
            case 2: $angry++; break;
            default: break;
        }
    }

    die(json_encode([
        'total' => $total,
        'happy' => $happy,
        'sad'   => $sad,
        'angry' => $angry,
    ]));
}

/**
 * Save Vote
 */
function save_emotion(
    $article_id, 
    $emotion_id
) {
    global $DB;
    $emotions = $DB->where('article_id', '=', $article_id)
                   ->where('user_id', '=', $_SESSION['id'])
                   ->first();
    
    if (!$emotions) {
         // Save DB
        $DB->create([
            'user_id'    => $_SESSION['id'],
            'article_id' => $article_id,
            'emotion_id' => $emotion_id,
        ]);

        die(json_encode([
            'message' => 'success',
        ]));

    } else if ($emotions->emotion_id == $emotion_id) {
        // Delete Data Dari DB jika data sama
        $DB->where('article_id', '=', $article_id)
           ->where('user_id', '=', $_SESSION['id'])
           ->delete();

        die(json_encode([
            'message' => 'unvote',
        ]));
    } else {
        // User ganti jenis vote
        $DB->where('article_id', '=', $article_id)
        ->where('user_id', '=', $_SESSION['id'])
        ->update([
            'emotion_id' => $emotion_id
        ]);

        die(json_encode([
            'message'     => 'changed',
            'old_emotion' => $emotions->emotion_id,
        ]));    

    }

}
