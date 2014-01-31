<?php echo $this->Sb->execBtn('--help', 'Show help');?>
<!--Modal for results -->
<div class="modal fade" id="results_modal" tabindex="-1" role="dialog" aria-labelledby="Results" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="results_title">Execution output</h4>
			</div>
			<div class="modal-body">
				<pre id="log">
				</pre>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
	function run_cmd(cmd) {
		$.ajax({
			url: '<?php echo $this->Html->url(array('action' => "execute_cmd")) ?>/' + cmd
		}).done(function(data) {
			$('#log').text(cmd+"\n"+data);
//			console.log(data);
//			$('#results_title').text(title);
			$('#results_modal').modal('show');
		});
	}
</script>