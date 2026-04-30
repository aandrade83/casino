# Script block templates

$HEADER = @'
<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/header.php");  ?>

<?php
$config = require $_SERVER['DOCUMENT_ROOT'].'/config.php';
$base_url = $config['app']['base_url'] ?? '';
?>
<script>
    var BASE_URL = "<?= $base_url ?>";
</script>
<script src="/utilities/js/config.js"></script>

'@

$SCRIPT_DECK_PLAIN = @'
<script type="text/javascript">
var current_balance = <?= isset($balance["amount"]) ? json_encode($balance["amount"]) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
var deck = new Array();
<?php foreach(isset($_deck) ? $_deck : [] as $card){ ?>
deck.push("<?= $card ?>");
<?php } ?>
</script>
'@

$SCRIPT_DECK_CARIB = @'
<script type="text/javascript">
var current_balance = <?= isset($balance["amount"]) ? json_encode($balance["amount"]) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode(round($_min_amount/2)) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode(round($_max_amount/2)) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
var deck = new Array();
<?php foreach(isset($_deck) ? $_deck : [] as $card){ ?>
deck.push("<?= $card ?>");
<?php } ?>
</script>
'@

$SCRIPT_DECK_THREE = @'
<script type="text/javascript">
var current_balance = <?= isset($balance["amount"]) ? json_encode($balance["amount"]) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode(round($_min_amount)) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode(round($_max_amount)) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
var deck = new Array();
<?php foreach(isset($_deck) ? $_deck : [] as $card){ ?>
deck.push("<?= $card ?>");
<?php } ?>
</script>
'@

$SCRIPT_SLOT = @'
<script type="text/javascript">
var current_balance = <?= isset($balance["amount"]) ? json_encode(round($balance["amount"],2)) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
var max_lines = 25;
</script>
'@

$SCRIPT_KENO = @'
<script type="text/javascript">
<? $freeplay = get_game_freeplays($_game->vars["id"], $_player->vars["id"], $_using_free_play ?? false); ?>
var free_spins = <?= isset($freeplay["total"]) ? json_encode(round($freeplay["total"])) : 0 ?>;
var current_balance = <?= isset($balance["amount"]) ? json_encode(round($balance["amount"],2)) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
</script>
'@

$SCRIPT_NODECK = @'
<script type="text/javascript">
var current_balance = <?= isset($balance["amount"]) ? json_encode($balance["amount"]) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
</script>
'@

# Game to template map

$gameTypeMap = @{
    'baccarat'                  = $SCRIPT_DECK_PLAIN
    'blackjack'                 = $SCRIPT_DECK_PLAIN
    'blackjack_contest'         = $SCRIPT_DECK_PLAIN
    'blackjack_double'          = $SCRIPT_DECK_PLAIN
    'blackjack_europe'          = $SCRIPT_DECK_PLAIN
    'blackjack_new'             = $SCRIPT_DECK_PLAIN
    'spanish_blackjack_contest' = $SCRIPT_DECK_PLAIN
    'holdem'                    = $SCRIPT_DECK_PLAIN
    'video_poker'               = $SCRIPT_DECK_PLAIN
    'video_poker_af'            = $SCRIPT_DECK_PLAIN
    'video_poker_deuces'        = $SCRIPT_DECK_PLAIN
    'caribbean'                 = $SCRIPT_DECK_CARIB
    'three_card'                = $SCRIPT_DECK_THREE
    'multi_slot'                = $SCRIPT_SLOT
    'multi_slot_alien'          = $SCRIPT_SLOT
    'multi_slot_crypto'         = $SCRIPT_SLOT
    'multi_slot_dragon'         = $SCRIPT_SLOT
    'multi_slot_FS'             = $SCRIPT_SLOT
    'multi_slot_FS2'            = $SCRIPT_SLOT
    'multi_slot_giants'         = $SCRIPT_SLOT
    'multi_slot_sweet'          = $SCRIPT_SLOT
    'multi_slot_viking'         = $SCRIPT_SLOT
    'multi_slot_west'           = $SCRIPT_SLOT
    'multi_slot_zeus'           = $SCRIPT_SLOT
    'keno'                      = $SCRIPT_KENO
    'wheel_of_fortune'          = $SCRIPT_KENO
    'craps'                     = $SCRIPT_NODECK
    'eroulette'                 = $SCRIPT_NODECK
    'roulette'                  = $SCRIPT_NODECK
    'classic_slot'              = $SCRIPT_NODECK
}

# Rebuild each file

$files = Get-ChildItem "D:\PROJECTS\CASINO\app\utilities\games\*\ui.php" | Sort-Object FullName

foreach ($file in $files) {
    $game = Split-Path (Split-Path $file.FullName) -Leaf

    if (-not $gameTypeMap.ContainsKey($game)) {
        Write-Host "SKIP (no type): $game"
        continue
    }

    $scriptBlock = $gameTypeMap[$game]

    # Extract body: take last 120 lines, find last <body>, grab from 3 lines before it
    $tail = Get-Content $file.FullName -Tail 120 -Encoding UTF8
    $bodyIdx = -1
    for ($i = $tail.Length - 1; $i -ge 0; $i--) {
        if ($tail[$i] -match '^\s*<body') { $bodyIdx = $i; break }
    }

    if ($bodyIdx -lt 0) {
        Write-Host "WARN (no body): $game - skipping"
        continue
    }

    # Start at <body>, but walk backwards to include any standalone PHP assignments
    # between </script> and <body> (e.g. "$equal_on_top = true"), excluding template content
    $startIdx = $bodyIdx
    for ($j = $bodyIdx - 1; $j -ge ([Math]::Max(0, $bodyIdx - 5)); $j--) {
        $prevLine = $tail[$j].Trim()
        if ($prevLine -match '^<\?' -and $prevLine -notmatch 'isset|json_encode|deck\.push|foreach|</script>') {
            $startIdx = $j
        } else {
            break
        }
    }
    $bodyLines = $tail[$startIdx..($tail.Length - 1)]
    $bodyContent = $bodyLines -join "`n"

    # Assemble clean file
    $newContent = $HEADER + $scriptBlock + "`n" + $bodyContent + "`n"

    Set-Content $file.FullName -Value $newContent -Encoding UTF8 -NoNewline
    Write-Host "OK  $game"
}
