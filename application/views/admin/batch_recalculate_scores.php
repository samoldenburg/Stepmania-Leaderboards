<p>
    Use this tool after the file parsing algorithm has been updated to recalculate all user scores to get updated applied difficulties as needed.<br />
    <em><strong>This tool is currently disabled.</strong></em>
</p>
<p id="ctime">
</p>
<div class="progress">
    <span class="meter" style="width: 0%; transition: width 0.5s ease;"></span>
</div>
<pre style="height: 300px; overflow-y: scroll; background: rgba(0,0,0,0.5); font-size: 11px;" id="output"></pre>
<script type="text/javascript">
    (function(a){var b=a({});a.ajaxQueue=function(c){function g(b){d=a.ajax(c).done(e.resolve).fail(e.reject).then(b,b)}var d,e=a.Deferred(),f=e.promise();b.queue(g),f.abort=function(h){if(d)return d.abort(h);var i=b.queue(),j=a.inArray(g,i);j>-1&&i.splice(j,1),e.rejectWith(c.context||c,[f,h,""]);return f};return f}})(jQuery)
    jQuery(document).ready(function($) {
        var scores_to_process = [
            <?php foreach ($scores as $score) : ?>
                ["<?=$score->id;?>", "<?=addslashes($score->username);?>", "<?=addslashes($score->rate);?>", "<?=addslashes($score->title);?>"],
            <?php endforeach; ?>
        ];
        var total = scores_to_process.length;
        var started = Math.floor(Date.now() / 1000);
        $("#recalculate").click(function() {
            started = Math.floor(Date.now() / 1000);
            var that = $(this);
            that.attr("disabled", "disabled");
            for (i = 0; i < total; i++) {
                parseScore(scores_to_process[i][0], scores_to_process[i][1], scores_to_process[i][2], scores_to_process[i][3], i, total, started);
            }
        });

        function parseScore(id, username, rate, title, count, total, time_started) {
            $.ajaxQueue({
                url: "/admin/recalculate_score/" + id,
                beforeSend: function( xhr ) {
                    xhr.overrideMimeType( "text/plain; charset=utf-8" );
                },
            })
            .done(function( data ) {
                $(".progress .meter").css("width", (((count+1)/total) * 100) + "%");
                var percent = (count+1)/total;
                var percent_formatted = percent * 100;
                var current_time = Math.floor(Date.now() / 1000);
                var time_from_start = (current_time - time_started);
                var estimated_time_remaining = (time_from_start / percent) - time_from_start;
                var string_time_from_start = time_from_start.toString();
                var string_estimated_time = estimated_time_remaining.toString();
                var content_str = "";
                content_str += percent_formatted + "% complete <br />";
                content_str += "Time Elapsed: " + string_time_from_start.toHHMMSS() + "<br />";
                content_str += "Time Remaining: " + string_estimated_time.toHHMMSS();
                $("p#ctime").html(content_str);

                $("#output").prepend(username + " - " + title + " : Rate " + rate + " -> " + data + "\n");
            });
        }

        String.prototype.toHHMMSS = function () {
            var sec_num = parseInt(this, 10); // don't forget the second param
            var hours   = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours   < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            if (seconds < 10) {seconds = "0"+seconds;}
            var time    = hours+':'+minutes+':'+seconds;
            return time;
        }
    });
</script>
