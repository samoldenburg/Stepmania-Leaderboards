<h1>Stepmania Leaderboards API</h1>

<p>
    Responses will be delivered via JSON. Use something like CURL to post data.
</p>

<p>
    Example implementation in PHP: <a href="http://pastebin.com/GECUCC55" target="_blank">http://pastebin.com/GECUCC55</a>
</p>

<h2>Perform a Full File Parse</h2>
<div class="apiblock">
    <span class="label">POST</span><code>http://smleaderboards.net/api/v1/parse/</code>
</div>
<h4>Accepted POST Variables</h4>
<div class="apiblock">
    <code>file</code> - The full file contents. Remember to urlencode.<br>
    <code>rate</code> - Only 0.5 to 2.0 is supported.<br>
    <code>verbose</code> - true/false, include extra, complicated information about the file.
</div>
<p>
    Retrieve pretty much all information you could really want about the file, including running the difficulty calculation.
</p>
<h4>Response</h4>
<pre style="font-size: 0.8em; max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 10px;">
[file_difficulty] => 19.3
[meta] => (
    [title] => Betsuni BETSUNI betsuni
    [subtitle] =>
    [artist] => Nico Nico Douga
    [rate] => 1x
    [length] => 00:35
    [length_in_seconds] => 35
    [dance_points] => 612
    [dance_points_from_holds] => 120
    [nontrivial_DP] => 262
    [dance_points_for_grade_AA] => 570
    [dance_points_for_grade_A] => 490
    [dance_points_for_grade_B] => 398
    [nontrivial_DP_needed_for_AA] => 220
    [alotted_misses_ignoring_free_DP] => 1
    [alotted_misses_NOT_ignoring_free_DP] => 4
    [miss_factor_between_these] => 4
    [weighted_AA_metric] => 72% (DP: 441)
    [NPS_adjustment_from_free_misses] => 0.95098039215686
    [notes] => 425
    [taps] => 246
    [jumps] => 150
    [hands] => 13
    [quads] => 1
    [holds] => 20
    [mines] => 0
    [left_hand_percent] => 51.294%
    [right_hand_percent] => 48.706%
    [peak_NPS] => 19
    [average_NPS] => 13.484848484848
    [bpms] =>
    [stops] =>
    [programmatically_derived_interval] => 0.5
)
</pre>
<p>
    <strong>Not included in example response (verbose only):</strong><br>
    <code>column_distributions</code> - In depth analysis on each measure of the file. Good luck deciphering this.<br>
    <code>formatted</code> - A formatted set of arrays for the notes in the file. Indexes can be passed through a float2rat function to determine specific beat/sub-beat
</p>

<h2>Parse File Meta Only</h2>
<div class="apiblock">
    <span class="label">POST</span><code>http://smleaderboards.net/api/v1/parse_meta/</code>
</div>
<h4>Accepted POST Variables</h4>
<div class="apiblock">
    <code>file</code> - The full file contents. Remember to urlencode.<br>
</div>
<p>
    Retrieve just some basic meta information about the file.
</p>
<h4>Response</h4>
<pre style="font-size: 0.8em; max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 10px;">
[title] => Betsuni BETSUNI betsuni
[subtitle] =>
[artist] => Nico Nico Douga
[rate] => 1x
[length] => 00:35
[length_in_seconds] => 35
[dance_points] => 612
[dance_points_from_holds] => 120
[nontrivial_DP] => 262
[dance_points_for_grade_AA] => 570
[dance_points_for_grade_A] => 490
[dance_points_for_grade_B] => 398
[nontrivial_DP_needed_for_AA] => 220
[alotted_misses_ignoring_free_DP] => 1
[alotted_misses_NOT_ignoring_free_DP] => 4
[miss_factor_between_these] => 4
[weighted_AA_metric] => 72% (DP: 441)
[NPS_adjustment_from_free_misses] => 0.95098039215686
[notes] => 425
[taps] => 246
[jumps] => 150
[hands] => 13
[quads] => 1
[holds] => 20
[mines] => 0
[left_hand_percent] => 51.294%
[right_hand_percent] => 48.706%
[peak_NPS] => 19
[average_NPS] => 13.484848484848
[bpms] =>
[stops] =>
[programmatically_derived_interval] => 0.5
</pre>
