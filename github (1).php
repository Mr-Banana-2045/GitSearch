<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Github User Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <style>
        .flows {
            border-radius: 50px;
            width: 200px;
        }
        .user {
            border-radius: 30px;
            width: 50px;
            margin-right: 10px;
        }
        .select2-container--default .select2-selection--single {
            height: 38px;
        }
        .select2-selection__rendered {
            line-height: 36px;
        }
    </style>
</head>
<body>
<center>
    <h1 style="padding:20px;">Search Engine</h1>
<form method="GET" class="form-inline justify-content-center">
    <div class="form-group mb-2">
        <label for="staticEmail" class="col-form-label" style="margin-right:10px;">Email</label>
        <input type="email" name="email" class="form-control" id="staticEmail2">
    </div>
    <button type="submit" style='margin-left:10px;' class="btn btn-primary mb-2">Config</button>
</form>
<?php
if (isset($_GET['email'])) {
    $url = "https://api.github.com/search/users?q=" . urlencode($_GET['email']);
    $options = [
        "http" => [
            "header" => "User-Agent: PHP\r"
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);
    
    if (isset($data['items']) && count($data['items']) > 0) {
        foreach ($data['items'] as $user) {
            echo "<img class='flows' src='" . htmlspecialchars($user['avatar_url']) . "'>" . "<p><strong>" . htmlspecialchars($user['login']) . "</strong><br><strong style='margin-top:10px;'><a href='" . htmlspecialchars($user['html_url']) . "'>" . htmlspecialchars($user['html_url']) . "</a></strong></p><br>";
            
            $flows = "https://api.github.com/users/" . htmlspecialchars($user['login']) . "/followers";
            $con = stream_context_create($options);
            $res = file_get_contents($flows, false, $con);
            $datas = json_decode($res, true);
            $followerCount = isset($datas) ? count($datas) : 0;
            if ($followerCount > 0) {
                echo '<div class="form-group">';
                echo '  <label for="followersSelect">Followers ' . $followerCount . '</label>';
                echo '  <select id="followersSelect" class="form-control" style="position:absolute; left:10px; right:10px; height:200px;" onchange="location = this.value;">';
                echo '  <option value="">Select a follower</option>';
                foreach ($datas as $flow) {
                    echo "<option data-image='" . htmlspecialchars($flow['avatar_url']) . "' value='" . htmlspecialchars($flow['html_url']) . "'>" . htmlspecialchars($flow['login']) . "</option>";
                }
                echo '  </select>';
                echo '</div>';
            }

            $repo = "https://api.github.com/users/" . htmlspecialchars($user['login']) . "/repos";
            $cons = stream_context_create($options);
            $ress = file_get_contents($repo, false, $cons);
            $dat = json_decode($ress, true);
            
            if (isset($dat) && count($dat) > 0) {
                echo '<div class="form-group">';
                echo '  <label for="reposSelect">Repository</label>';
                echo '  <select multiple id="reposSelect" class="form-control" style="position:absolute; bottom:5px; left:10px; right:50px; height:200px;" onchange="location = this.value;">';
                foreach ($dat as $repos) {
                    echo "<option value='" . htmlspecialchars($repos['html_url']) . "'>" . htmlspecialchars($repos['full_name']) . "</option>";
                }
                echo '  </select>';
                echo '</div>';
            }
        }
    }
}
?>
</center>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#followersSelect').select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var img = $(state.element).data('image');
            var $state = $(
                '<span><img src="' + img + '" class="user" /> ' + state.text + '</span>'
            );
            return $state;
        }
    });
</script>
</body>
</html>
