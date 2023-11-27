<script setup>
import axios from 'axios';
import { useAppVariableStore } from '@/stores/app-variable';

const appVariable = useAppVariableStore();



</script>
<template>
	<div>
		<!-- BEGIN row -->
		<div class="row mb-3">
			<!-- BEGIN col-6 -->
			<div class="col-xl-6" style="width: 100%;">
				<!-- BEGIN panel -->
				<panel>
					<panel-header>
						<panel-title>Form Controls</panel-title>
						<panel-toolbar />
					</panel-header>
					<panel-body>
						<form>
							<div class="row mb-15px">
								<label class="form-label col-form-label col-md-3">Email address</label>
								<div class="col-md-9">
									<input type="email" class="form-control mb-5px" placeholder="Enter email" />
									<small class="fs-12px text-gray-500-darker">We'll never share your email with anyone else.</small>
								</div>
							</div>
							<div class="row mb-15px">
								<label class="form-label col-form-label col-md-3">Example select</label>
								<div class="col-md-9">
									<select class="form-select">
										<option>1</option>
										<option>2</option>
										<option>3</option>
										<option>4</option>
										<option>5</option>
									</select>
								</div>
							</div>
							<div class="row mb-15px">
								<label class="form-label col-form-label col-md-3">Example multiple select</label>
								<div class="col-md-9">
									<select multiple class="form-select">
										<option>1</option>
										<option>2</option>
										<option>3</option>
										<option>4</option>
										<option>5</option>
									</select>
								</div>
							</div>
							<div class="row mb-15px">
								<label class="form-label col-form-label col-md-3">Example textarea</label>
								<div class="col-md-9">
									<textarea class="form-control" rows="3"></textarea>
								</div>
							</div>
						</form>
					</panel-body>
				</panel>
				<!-- END panel -->
			</div>
			<!-- END col-6 -->
		</div>
		<!-- END row -->
	</div>
</template>