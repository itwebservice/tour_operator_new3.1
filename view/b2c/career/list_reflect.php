<?php
include_once("../../../model/model.php");
$query = mysqlQuery("SELECT * FROM `b2c_career` where active_flag='0' ");
?>
<div class="row"> <div class="col-md-12"> <div class="table-responsive">

<table class="table table-hover" id="tbl_list" style="margin: 20px 0 !important;">
	<thead>
		<tr class="table-heading-row">
			<th>S_No.</th>
			<th>Position</th>
			<th>Location</th>
            <th>Job Type</th>
            <!-- <th>Job Description</th>
            <th>Skills</th>
            <th>Benefits</th> -->
            <th>Edit</th>
		</tr>
	</thead>
	<tbody>
    <?php
    $count = 0;
	while($row_query = mysqli_fetch_assoc($query)){

		// $url = $row_query['image'];
		$description = $row_query['description'];
		$position = $row_query['position'];
        $location = $row_query['location'];
        $job_type = $row_query['job_type'];
        $job_description = $row_query['job_description'];
        $skills = $row_query['skills'];
        $benefits = $row_query['benefits'];
		$bg = ($row_query['active_flag'] == '1') ? 'danger' : '';
		?>
			<tr class="<?= $bg ?>">
				<td><?= ++$count ?></td>
				<td><?= $position ?></td>
                <td><?= $location ?></td>
                <td><?= $job_type ?></td>
                <!-- <td><?= $job_description ?></td>
                <td><?= $skills ?></td>
                <td><?= $benefits ?></td> -->
				<td>
					<button class="btn btn-info btn-sm" onclick="update_modal(<?= $row_query['entry_id'] ?>)" title="Update Details" id="updateb_btn-<?= $row_query['entry_id'] ?>"><i class="fa fa-pencil-square-o"></i></button>
				</td>
			</tr>
		<?php
	} ?>
	</tbody>
</table>

</div> </div> </div>
<script>
$('#tbl_image_list').dataTable({
	"pagingType": "full_numbers"
});
</script>