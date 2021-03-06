<!-- List Timer Items -->
<div class="row justify-content-end" style="padding-bottom: 20px;">
    <form name="ccc_post" method="POST" action="<?php echo get_site_url(); ?>/timers/">
        <input type="hidden" name="calibration_calculation" value="false" />
    <?php
        if(hasRole('CALIBRATOR')){
    ?>
        <button class="btn btn-sm btn-outline-dark" onclick="document.ccc_post.submit()">
            <strong><span aria-hidden="true">&plus;</span> New Calculation </strong>
        </button>
    <?php
        }
    ?>
    </form>
</div>
<div class="">
    <table class="table table-striped table-sm table-bordered" style="font-size: 0.8rem;" id="timers-list" data-page-length="25">
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Client</th>
                <th scope="col">Equipment</th>
                <th scope="col">Serial Number</th>
                <th scope="col">Result</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($certicates as $certicate) {
                    echo "<tr><td>$certicate->date_performed</td>";
                    echo "<td>$certicate->client_name</td>";
                    echo "<td>$certicate->equipment_name</td>";
                    echo "<td>$certicate->equipment_serial_number</td>";
                    $badge = ['FAIL'=>'danger', 'PASS'=>'success', 'PENDING'=>'warning'];
                    echo "<td><span class='badge badge-".$badge[$certicate->result]."'>".$certicate->result."</span></td>";
            ?>
                    <td>
                        <form name="ccc_cert" id="ccc_cert" method="POST"
                            action="<?php echo get_site_url(); ?>/timers/" class="inline-form">
                            <input type="hidden" name="show_calibration_certificate" value="false" />
                            <input type="hidden" name="ccc_id" value="<?php echo $certicate->id; ?>" />
                            <button class="btn btn-sm btn-outline-success" onclick="document.getElementById('ccc_cert').submit()">View</button>
                        </form>
                        <form name="ccc_cert_1" id="ccc_cert_1" method="POST"
                            action="<?php echo get_site_url(); ?>/timers/" class="inline-form">
                            <input type="hidden" name="edit_calibration_calculation" value="false" />
                            <input type="hidden" name="ccc_id" value="<?php echo $certicate->id; ?>" />
                            <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('ccc_cert_1').submit()">Edit</button>
                        </form>
                    </td></tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>
<!-- / List Timer Items -->
