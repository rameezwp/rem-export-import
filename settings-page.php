<div class="wrap ich-settings-main-wrap">
	<div class="row">
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e( 'Export Options', 'rem-expimp' ); ?></h3>
				</div>
				<div class="panel-body">
					<div class="alert alert-info">
						<?php _e( 'If there are a large number of properties you want to migrate, we will recommed you use', 'rem-expimp' ); ?> <a target="_blank" href="https://wordpress.org/plugins/real-estate-manager-importer-for-wp-all-import/"><strong>REM Importer.</strong></a>
					</div>
					<p>
						<?php _e( 'It will export your desired option as a', 'rem-expimp' ); ?> <code>.json</code> <?php _e( 'file', 'rem-expimp' ); ?>.
						<?php _e( 'Then you can easily import that file into another site.', 'rem-expimp' ); ?>
					</p>
					<br>
					<form method="post" class="form-horizontal">
						<input type="hidden" name="rem_export_ppts" value="export_all_properties" />

						<div class="form-group">
							<label for="chooseproperties" class="col-sm-3 control-label"><?php _e( 'Choose Export', 'rem-expimp' ); ?></label>
							<div class="col-sm-9">
								<select name="chooseproperties" id="chooseproperties" class="form-control">
									<option value="all"><?php _e( 'All Properties', 'rem-expimp' ); ?></option>
									<option value="id"><?php _e( 'ID Specific Properties', 'rem-expimp' ); ?></option>
									<option value="agent"><?php _e( 'Agent Specific Properties', 'rem-expimp' ); ?></option>
									<option value="settings"><?php _e( 'Settings', 'rem-expimp' ); ?></option>
									<option value="property_fields"><?php _e( 'Property Fields', 'rem-expimp' ); ?></option>
									<option value="registration_fields"><?php _e( 'Registration Fields', 'rem-expimp' ); ?></option>
								</select>
							</div>
						</div>

						<div class="form-group show-if-id">
							<label for="p_ids" class="col-sm-3 control-label"><?php _e( 'Property IDs', 'rem-expimp' ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="p_ids">
								<p class="help-block"><?php _e( 'Provide comma separated IDs here.', 'rem-expimp' ); ?></p>
							</div>
						</div>

						<div class="form-group show-if-agent">
							<label for="agent_ids" class="col-sm-3 control-label"><?php _e( 'Agent IDs', 'rem-expimp' ); ?></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="agent_ids">
								<p class="help-block"><?php _e( 'Provide comma separated IDs here.', 'rem-expimp' ); ?></p>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-9">
								<?php wp_nonce_field( 'rem_export_nonce', 'rem_export_nonce' ); ?>
								<input type="submit" class="btn btn-primary" value="Export">
							</div>
						</div>
						<p class="text-center">
						</p>
					</form>					
				</div>
			</div>			
		</div>
		<div class="col-sm-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e( 'Import Options', 'rem-expimp' ); ?></h3>
				</div>
				<div class="panel-body">
					<div class="alert alert-warning">
						<?php _e( 'Make sure to always have a backup before importing.', 'rem-expimp' ); ?>
					</div>
					<form class="form-horizontal" method="post" enctype="multipart/form-data">
						<div class="form-group">
							<label class="col-sm-4 control-label"><?php _e( 'What to Import?', 'rem-expimp' ); ?></label>
							<div class="col-sm-8">
								<select name="importoptions" class="form-control">
									<option value="properties"><?php _e( 'Properties', 'rem-expimp' ); ?></option>
									<option value="settings"><?php _e( 'Settings', 'rem-expimp' ); ?></option>
									<option value="property_fields"><?php _e( 'Property Fields', 'rem-expimp' ); ?></option>
									<option value="registration_fields"><?php _e( 'Registration Fields', 'rem-expimp' ); ?></option>
								</select>
							</div>
						</div>	
						<div class="form-group">
							<label class="col-sm-4 control-label"><?php _e( 'Upload File', 'rem-expimp' ); ?></label>
							<div class="col-sm-8">
								<input type="file" name="import_file"/>
							</div>
						</div>
						<p>
							<input type="hidden" name="rem_imp_ppts" value="import_properties" />
							<?php wp_nonce_field( 'rem_import_nonce', 'rem_import_nonce' ); ?>
							<input type="submit" class="btn btn-primary" value="Import">
						</p>
					</form>
					<?php if (isset($_GET['success_import'])) { ?>
						<div class="alert alert-success">
							<?php _e( 'Import Process Done!', 'rem-expimp' ); ?>
						</div>
					<?php } ?>
				</div>
			</div>			
		</div>
	</div>
</div>