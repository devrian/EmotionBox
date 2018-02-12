<?php

require_once 'init.php';
// ===Get Article===
$article_id = $_GET['id'];
$DB->setTable('articles');
$article = $DB->where('id', '=', $article_id)->first();

$emotions = ['happy', 'sad', 'angry'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $article->title; ?></title>
    <link rel="stylesheet" href="assets/css/main.css">

</head>
<body>
    <h2><?= $article->title; ?></h2>
    <p>
        <?= $article->subject; ?>    
    </p>

    <div id="emotionBox">
        <?php for ($i=0; $i<count($emotions); $i++) : ?>
            <div class="emotions" @click="vote(<?= $i ?>)">
                <img src="assets/images/<?= $emotions[$i]; ?>.png" alt="">
                
                <span v-if="totalIsZero">
                    0 %
                </span>

                <span v-else>
                    {{ parseInt(<?=$emotions[$i]?>/total*100) }}%
                </span>

                <span class="total">Total {{ <?= $emotions[$i] ?> }}</span>
            </div>
        <?php endfor; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.13/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-resource@1.3.5"></script>
    <script>
        var vm = new Vue({
            el: "#emotionBox",
            data: {
                article_id: <?= $article_id ?>,
                total: 0,
                happy: 0,
                sad: 0,
                angry: 0,
            },
            computed: {
                totalIsZero: function() {
                    if (this.total != 0) {
                        return false;
                    }

                    return true;
                }
            },
            created: function() {
                this.$http({
                    url: 'ajax/emotion.php?action=all_data&article_id=' + this.article_id,
                    method: 'GET'
                }).then(function(response) {
                    _resp = JSON.parse(response.bodyText);
                    if (_resp.total != 0) {
                        this.total = _resp.total;
                        this.happy = _resp.happy;
                        this.sad   = _resp.sad;
                        this.angry = _resp.angry;
                    }
                })
            },
            methods: {
                vote(val) {
                    this.$http({
                        url: 'ajax/emotion.php?action=vote&article_id=' + this.article_id + '&emotion_id=' + val,
                        method: 'GET'
                    }).then(function(response) {
                        _resp = JSON.parse(response.bodyText);
                        console.log(_resp);

                        if (_resp.message == 'unvote') {
                            this.total--;
                            this.modify(val, -1);
                        } else if (_resp.message == 'success') {
                            this.total++;
                            this.modify(val, 1);
                        } else {
                            this.modify(parseInt(_resp.old_emotion), -1);
                            this.modify(val, 1);
                        }
                    })
                },
                modify(val, point) {
                    switch (val) {
                        case 0:
                            this.happy = this.happy + point;
                            break;
                        case 1:
                            this.sad = this.sad + point;
                            break;
                        case 2:
                            this.angry = this.angry + point;
                            break;
                    
                        default:
                            break;
                    }
                }
            }
        });
    </script>
</body>
</html>