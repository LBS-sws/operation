<style>
    .ranking-note-body>dl>dd{ padding-left: 15px;}
    .ranking-note{
        position: fixed;
        background: #fff;
        top:17%;
        z-index: 10;
        right: 15px;
        width: 35%;
        border-radius: 3px;
        border: 1px solid #d2d6de;
        box-shadow: 0 2px 7px rgba(0,0,0,0.1);
    }
    .ranking-note-body{
        height: 500px;
        padding: 5px 10px 0px 25px;
        overflow-y: scroll;
    }
    .note-click{ position: absolute;top:0px;left:0px;display:table;width: 20px;height:100%;text-align: center;}

    .note-click:before{ content: " ";display: table-cell;vertical-align: middle;width: 0px;}
    .middle-span{ display: table-cell;vertical-align: middle;background: #f4f4f4;border-right: 1px solid #d2d6de}

    .ranking-note.active{ width: 20px;overflow: hidden;}
    .note-click.active>.fa-angle-double-right:before{ content: "\f100"}
</style>
<div class="ranking-note" style="">
    <a class="note-click" href="javascript:void(0);">
        <span class="middle-span fa fa-angle-double-right"></span>
    </a>
    <div class="ranking-note-body">
        <h4><?php echo Yii::t("rank","Technical department comprehensive ranking score conditions");?></h4>
        <dl>
            <dt><?php echo Yii::t("rank","1, credits: one-time 50/ point, score *50");?></dt>
            <dt><?php echo Yii::t("rank","2, charity points: one-time 500/ points, points *500");?></dt>

            <dt class="dt-click"><?php echo Yii::t("rank","3. Number of badges: 50 per month (by type)");?></dt>
            <dd></dd>
            <dd><?php echo Yii::t("rank","Example: Xiao Zhao got the first cleaning and pest control badges on January 15, 2022.1, so his badge score in January is 2*50=100, and he will get the same 100 points every month from January. If he got the communication badges on March 2, his badge score in March will be 100+50=150");?></dd>

            <dt class="dt-click"><?php echo Yii::t("rank","4. Service amount");?></dt>
            <dd>
                <dl class="dl-horizontal" style="margin: 0px;">
                    <dt>0-20000：</dt>
                    <dd>0.1</dd>
                    <dt>20001-25000：</dt>
                    <dd>0.08</dd>
                    <dt>25001-30000：</dt>
                    <dd>0.07</dd>
                    <dt>30001-40000：</dt>
                    <dd>0.05</dd>
                    <dt>40001-50000：</dt>
                    <dd>0.03</dd>
                    <dt>50001-70000：</dt>
                    <dd>0.02</dd>
                    <dt>70001以上：</dt>
                    <dd>0.01</dd>
                </dl>
                <?php echo Yii::t("rank","Computation by business forehead layer, if have 35500, mark should be");?><br/>20000*0.1+5000*0.08+5000*0.07+5500*0.05
            </dd>
            <?php
            $sqlDate = RankingMonthForm::getSqlDate($model->rank_year,$model->rank_month);
            if(count($sqlDate)>=15){
                echo "<dt>".Yii::t("rank","5, night score title")."</dt>";
                echo "<dd>".Yii::t("rank","night score body")."</dd>";
                echo "<dd>".Yii::t("rank","night score body two")."</dd>";
                echo "<dt>".Yii::t("rank","6, create score title")."</dt>";
                echo "<dd>".Yii::t("rank","create score body")."</dd>";
                echo "<dd>".Yii::t("rank","create score body two")."</dd>";
                $i=6;
            }else{
                $i=4;
            }
            ?>

            <dt><?php echo ($i+1).Yii::t("rank",", praise letters: 100 points for each letter");?></dt>
            <dt><?php echo ($i+2).Yii::t("rank",", customer complaint follow-up (not their own customers) : 100 points/order");?></dt>

            <dt class="dt-click"><?php echo ($i+3).Yii::t("rank",". Quality inspection (monthly average score, at least one quality inspection of our own customer)");?></dt>
            <dd><?php echo Yii::t("rank","Score calculation");?>：<br/>
                <dl class="dl-horizontal" style="margin: 0px;">
                    <dt><?php echo Yii::t("rank","80 the following");?>：</dt>
                    <dd>0</dd>
                    <dt>81-85：</dt>
                    <dd>100</dd>
                    <dt>86-90：</dt>
                    <dd>200</dd>
                    <dt>91-95：</dt>
                    <dd>300</dd>
                    <dt><?php echo Yii::t("rank","More than 95");?>：</dt>
                    <dd>500</dd>
                </dl>
                <?php echo Yii::t("rank","(1+ Number of customers *0.02) * Tiered conversion points");?>
            </dd>
            <dt class="dt-click"><?php echo ($i+4).Yii::t("rank",". Optimize talent evaluation scores");?></dt>
            <dd>
                <table class="table table-bordered table-condensed" style="width: 380px !important;margin: 0 auto;">
                    <tbody>
                    <tr>
                        <td class="text-center"><?php echo Yii::t("rank","Below 60");?>：0</td>
                        <td class="text-center">60-63：150</td>
                        <td class="text-center">64-66：200</td>
                    </tr>
                    <tr>
                        <td class="text-center">67-69：250 </td>
                        <td class="text-center">70-72：350 </td>
                        <td class="text-center">73-75：450</td>
                    </tr>
                    <tr>
                        <td class="text-center">76-77：550</td>
                        <td class="text-center">78-79：650</td>
                        <td class="text-center">80-85：800</td>
                    </tr>
                    <tr>
                        <td class="text-center">86-90：950</td>
                        <td class="text-center">91-95：1100</td>
                        <td class="text-center">96-100：1300</td>
                    </tr>
                    </tbody>
                </table>
                <?php echo Yii::t("rank","Note: This score will be added to the semiannual and annual leaderboards only after the evaluation of january-June of the current year is completed. The corresponding score will be displayed to the corresponding year after the evaluation of July to December of the current year is completed in the next year (shown in the semiannual and annual leaderboards).");?>
            </dd>
            <dt class="dt-click"><?php echo ($i+5).Yii::t("rank",". Envelopes of appreciation");?></dt>
            <dd>
                <span><?php echo Yii::t("rank","1 star: 50 points per piece");?></span><br/>
                <span><?php echo Yii::t("rank","2 star: 100 points per piece");?></span><br/>
                <span><?php echo Yii::t("rank","3 star: 200 points per piece");?></span><br/>
                <span><?php echo Yii::t("rank","Four stars and five stars have no score");?></span>
            </dd>
            <dt><?php echo ($i+6).Yii::t("rank",". Introduction of new technicians: 500 points per technician after 3 months");?></dt>
            <dt><?php echo ($i+7).Yii::t("rank",". Two services (IA+IB): 300 extra points per month");?></dt>
            <dt><?php echo ($i+8).Yii::t("rank",", introduce new business: order: 500 points/order");?></dt>
            <dt><?php echo ($i+9).Yii::t("rank",". Xidiyi sales: 100 cents per barrel (free delivery does not count)");?></dt>
        </dl>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $(".note-click").click(function () {
            if($(this).hasClass("active")){
                $(this).removeClass("active");
                $(this).parent('.ranking-note').removeClass("active");
                localStorage.setItem("rankingNote",0);
            }else{
                $(this).addClass("active");
                $(this).parent('.ranking-note').addClass("active");
                localStorage.setItem("rankingNote",1);
            }
        });
        if(localStorage.getItem("rankingNote")==1){
            $(".note-click").trigger("click");
        }
    })
</script>