<?php
$previous_scan = $context->getPreviousScan();
$site = $context->getSite();
$pages = $context->getPages();
$site_pass_fail = $context->isPassFail();
?>

<div class="scan">
    <header>
        <h2>
            <?php
            $date = date("n-j-y g:i a", strtotime($context->start_time));
            if (!$context->isComplete()) {
                $date = '--';
            }
            ?>
            Scan: <?php echo $date ?>
        </h2>
        <div class="sub-info">
            Status: <?php echo $context->status;?>
            <?php
            if (!$context->isComplete()) {
                echo $savvy->render($context->getProgress());
            }
            ?>
        </div>
    </header>
    <?php
    if (!$context->isComplete()) {
        ?>
        <div class="panel notice">
            This scan has not finished yet.  This page will automatically refresh when the scan has completed.
        </div>
    <?php
    }
    ?>

    <?php
    if ($site_pass_fail && $context->isComplete()) {
        $passing = false;
        if ($context->gpa == 100) {
            $passing = true;
        }
        ?>
        <div class="dashboard-metrics">
            <div class="visual-island site-pass-fail-status <?php echo ($passing)?'valid':'invalid'; ?>">
                <span class="dashboard-value">
                    <?php
                    if ($passing) {
                        echo 'Looks Good';
                    } else {
                        echo 'Needs Work';
                    }
                    ?>
                </span>
                <span class="dashboard-metric">
                    <?php
                    if ($passing) {
                        echo 'All of your pages are passing.  Good job!';
                    } else {
                        echo 'In order for the site to pass, all pages must pass.';
                    }
                    ?>
                </span>
            </div>
        </div>
    <?php
    }
    ?>
    
    <section class="wdn-grid-set dashboard-metrics">
        <div class="bp2-wdn-col-one-fourth">
            <div class="visual-island gpa">
                <span class="dashboard-value">
                    <?php
                    if ($context->isComplete()) {
                        echo $context->gpa . ($site_pass_fail?'%':'');
                    } else {
                        echo '--';
                    }
                    ?>
                </span>
                <?php 
                $gpa_name = 'GPA';
                if ($site_pass_fail) {
                    $gpa_name = 'of pages are passing';
                }
                ?>
                <span class="dashboard-metric"><?php echo $gpa_name ?></span>
            </div>
        </div>
        <div class="bp2-wdn-col-one-fourth">
            <div class="visual-island">
                <?php
                $arrow = "&#8596; <span class='secondary'>(same)</span>";
                if ($previous_scan) {
                    if ($previous_scan->gpa > $context->gpa) {
                        $arrow = "&#8595; <span class='secondary'>(worse)</span>";
                    } else if ($previous_scan->gpa < $context->gpa) {
                        $arrow = "&#8593; <span class='secondary'>(better)</span>";
                    }
                    
                    if ($site_pass_fail != $previous_scan->isPassFail()) {
                        $arrow = "&#8800; <span class='secondary'>(incomparable)</span>";
                    }
                }
                ?>
                <div class="dashboard-value">
                    <?php echo $arrow ?>
                </div>
                <div class="dashboard-metric">Compared to Last Scan</div>
            </div>
        </div>
        <div class="bp2-wdn-col-one-fourth">
            <div class="visual-island">
                <div class="dashboard-value">
                    <?php echo $context->getABSNumberOfChanges()  ?>
                </div>
                <div class="dashboard-metric">Change(s) Since Last Scan</div>
            </div>
        </div>
        <div class="bp2-wdn-col-one-fourth">
            <div class="visual-island">
                <span class="dashboard-value"><?php echo $pages->count() ?></span>
                <span class="dashboard-metric">Pages</span>
            </div>
        </div>
    </section>
    
    <section>
        <?php
        if ($previous_scan) {
            $changes = $context->getChangedMetricGrades();
            if ($changes->count() > 20) {
                ?>
                <p class="change-list-first">
                    We suppressed the change list because there were too many changes. <a href="<?php echo $context->getURL() . 'changes/' ?>"> View the changes</a>
                </p>
            <?php
            } else {
                echo $savvy->render($changes);
            }
        } else {
            //This is the first scan, don't the change list would probably be huge
            ?>
            <p class="change-list-first">
                Normally, a list of changes would be here.  However, this is the first time that we scanned your site.  In the future, you can see changes here.
            </p>
        <?php
        }
        ?>

        <div class="wdn-grid-set">
            <div class="bp1-wdn-col-three-sevenths">
                <section class="hot-spots info-section">
                    <header>
                        <h3>Hot Spots</h3>
                        <div class="subhead">
                            These are areas on your site that need some love
                        </div>
                    </header>
                    <?php
                    foreach ($plugin_manager->getMetrics() as $metric) {
                        $metric_record = $metric->getMetricRecord();
                        $grades = $context->getHotSpots($metric_record->id);
                        ?>
                        <h4><?php echo $metric->getName()?></h4>
                        <?php echo $savvy->render($grades); ?>
                    <?php
                    }
                    ?>
                </section>
            </div>
            <div class="bp1-wdn-col-four-sevenths">
                <?php
                echo $savvy->render($pages);
                ?>
            </div>
        </div>
    </section>
</div>
