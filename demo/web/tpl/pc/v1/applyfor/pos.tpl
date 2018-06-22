{include file="header.tpl"}
<script src="{$static}/script/city/cities.js"></script>
<script src="{$static}/script/city/city.js"></script>

<script>
	$(document).ready(function() {
		// 初始化城市
		$.city.init({
			cityid: "",
			province: "data[p_province]",
			city: "data[p_city]",
			area: "data[p_county]",
		});

		// 选择图片，预览
		$(".imginput").find("input[type='file']").change(function(){
			var file = $(this).get(0).files[0];
			var url = $.img.url(file);
			if (url) {
				$(this).prev().attr("src", url);
			}
		});

		$("#getSMSCodes").click(function(){
			var t = this;
			var mobile = $("input[name='data[p_linkman_mobile]']").val();
			var captcha = $("input[name='pic_code']").val();
			if (mobile.trim() == "") {
				$("input[name='data[p_linkman_mobile]']").focus();
				return false;
			}
			if (captcha.trim() == "") {
				$("input[name='pic_code']").focus();
				return false;
			}
			$.ajax({
				url: "/ajax/authcode/send",
				data: { mobile: mobile, captcha: captcha },
				success: function(res) {
					$(t).attr("disabled", true);
					var seconds = 60;
					var timer = setInterval(function(){
						seconds -= 1;
						$(t).text(seconds + " 秒后重新获取");
						if (seconds == 0) {
							clearInterval(timer);
							$(t).text("获取短信验证码").attr("disabled", false);
						}
					}, 1000);
				}
			});
		});

	    $('#pos_form').formValidation({
			locale: 'zh_CN',
			icon: {
				valid: 'fas fa-check',
				invalid: 'fas fa-times',
				validating: 'fas fa-sync-alt'
			},
			fields: {
				"data[p_name]": {
					validators: {
						stringLength: { enabled: true, min: 4, message: '必须大于等于4个字' }
					}
				},
				"data[p_fullname]": {
					validators: {
						stringLength: { min: 5, message: '必须大于等于5个字' }
					}
				},
				"data[p_address]": {
					validators: {
						stringLength: { min: 10, message: '必须大于10个字' }
					}
				},
				"data[p_linkman]": {
					validators: {
						stringLength: { min: 2, message: '必须大于等于2个字' }
					}
				},
				"data[p_linkman_mobile]": {
					validators: {
						{literal}
						regexp: { regexp: /^1[34578]{1}\d{9}$/, message: '必须是手机号码' }
						{/literal}
					}
				},
				"pic_code": {
					validators: {
						stringLength: { min: 4, max: 4, message: "图片验证码必须是4位" },
						remote: { url: '/ajax/captcha', message: '图片验证码不正确' }
					}
				},
				"sms_code": {
					validators: {
						stringLength: { min: 4, max: 4, message: "短信验证码必须是4位" }
					}
				},
				"agree": {
					validators: {
						notEmpty: { message: '必须同意POS门店合作协议才能提交申请单' }
					}
				}
			}
	    });
	});
</script>
</head>
<body>
	{include file="nav.tpl"}
	<div class="container applyfor">
		<div class="row">
			<div class="col-3">
				{include file="pc/v1/applyfor/menu.tpl"}
			</div>
			<div class="col-9">
				<div class="accordion" id="applyfor">
					<form id="pos_form" method="post" enctype="multipart/form-data">
					<h4 class="tc">POS门店加盟申请单</h4>
					<div class="ptb1 text-secondary fs12">注：请您认真填写以下信息，收到您的申请后我们的客服人员会第一时间联系您。资料越详细越好。愿能早日达成合作！</div>
					<div class="card">
						<div class="card-header" id="headingBaseInfo">
							<h6 class="mb-0" data-toggle="collapse" data-target="#baseInfo" aria-expanded="true" aria-controls="baseInfo">门店基本信息（必填） <i class="fas fa-chevron-down float-right"></i></h6>
						</div>
						<div id="baseInfo" class="collapse show" aria-labelledby="headingBaseInfo">
							<div class="card-body">
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 门店简称</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_name]" value="" placeholder="请输入门店简称" title="请输入门店简称" required />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 门店全称</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_fullname]" placeholder="请输入门店全称" title="请输入门店全称" required />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 经营类型</label>
											<div class="col-sm-8">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[p_type]" id="p_type_1" value="1">
													<label class="form-check-label form-control-sm" for="p_type_1">合营</label>
												</div>
<!-- 												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[p_type]" id="p_type_2" value="2">
													<label class="form-check-label form-control-sm" for="p_type_2">直营</label>
												</div> -->
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[p_type]" id="p_type_3" value="3" checked />
													<label class="form-check-label form-control-sm" for="p_type_3">加盟</label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 所在城市</label>
											<div class="col-sm-10">
												<select class="selectpicker" data-style="btn btn-sm bg-white btn-select-sm w160" data-live-search="true" name="data[p_province]" title="省/直辖市" required>
												</select>
												<select class="selectpicker" data-style="btn btn-sm bg-white btn-select-sm w160" name="data[p_city]" title="市" required>
												</select>
												<select class="selectpicker" data-style="btn btn-sm bg-white btn-select-sm w160" name="data[p_county]" title="区/县" required>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 地址</label>
											<div class="col-sm-7">
												<input type="text" class="form-control form-control-sm" name="data[p_address]" placeholder="请输入门地址" title="请输入门地址" required />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">营业执照</label>
											<div class="col-sm-8">
												<div class="imginput imginput-bl"><img id="blImg" src="{$static}/img/bl_150_200.png" /><input type="file" name="bl"  title="请上传营业执照图片" /></div>
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">统一社会信用代码</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_uscc]" placeholder="请输入门统一社会信用代码" />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">场地类型</label>
											<div class="col-sm-8">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[p_premises_type]" id="p_premises_type_1" value="1">
													<label class="form-check-label form-control-sm" for="p_premises_type_1">自有</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[p_premises_type]" id="p_premises_type_2" value="2" checked>
													<label class="form-check-label form-control-sm" for="p_premises_type_2">租赁</label>
												</div>
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">场地面积</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_premises_area]" placeholder="请输入场地面积 如：2000" />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 联系人</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_linkman]" value="" placeholder="请输入门店联系人" title="请输入门店联系人" required />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 联系人手机</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_linkman_mobile]" placeholder="请输入门店联系人手机" title="请输入门店联系人手机" required />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 图片验证码</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="pic_code" placeholder="请输入图片验证码" maxlength="4" required />
												<img class="btn btn-outline-secondary pd0 bg-white" style="height: 40px; margin-top: 5px;" src="/captcha?codeName=picCode">
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr"><i class="text-danger">*</i> 短信验证码</label>
											<div class="col-sm-8">
												<div class="input-group input-group-sm">
													<input type="text" class="form-control form-control-sm" name="sms_code" placeholder="请输入短信验证码" required />
													<div class="input-group-append">
														<button id="getSMSCodes" class="btn btn-outline-secondary" type="button">获取短信验证码</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-header" id="headingTwo">
							<h6 class="mb-0" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">门店法人信息（选填） <i class="fas fa-chevron-right float-right"></i></h6>
						</div>
						<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo">
							<div class="card-body">
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">法人姓名</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_legal_person]" value="" placeholder="请输入门店法人姓名" />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">法人身份证号码</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_legal_person_id]" placeholder="请输入门店法人身份证号码" />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">法人身份证正面</label>
											<div class="col-sm-8">
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">法人身份证反面</label>
											<div class="col-sm-8">
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">法人手机</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_legal_person_mobile]" value="" placeholder="请输入门店法人手机" />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">法人邮箱</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[p_legal_person_email]" placeholder="请输入门店法人邮箱" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-header" id="headingThree">
							<h6 class="mb-0" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">门店银行帐号（选填） <i class="fas fa-chevron-right float-right"></i></h6>
						</div>
						<div id="collapseThree" class="collapse" aria-labelledby="headingThree">
							<div class="card-body">
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">帐户类型</label>
											<div class="col-sm-8">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[pb_type]" id="pb_type_1" value="1">
													<label class="form-check-label form-control-sm" for="pb_type_1">基本户</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="data[pb_type]" id="pb_type_2" value="2" checked>
													<label class="form-check-label form-control-sm" for="pb_type_2">资金来往帐户</label>
												</div>
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">开户银行</label>
											<div class="col-sm-8">
												<select class="selectpicker" data-style="btn btn-outline-secondary btn-sm bg-white btn-select-sm form-control" data-live-search="true" name="data[pb_bank]" title="请选择银行">
													<option></option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">账户名称</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[pb_account_name]" value="" placeholder="请输入账户名称" />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">账户</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[pb_account_number]" placeholder="请输入账户" />
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">财务联系人</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[pb_linkman]" value="" placeholder="请输入财务联系人" />
											</div>
										</div>
									</div>
									<div class="col-6">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label col-form-label-sm tr">财务联系人手机</label>
											<div class="col-sm-8">
												<input type="text" class="form-control form-control-sm" name="data[pb_linkman_mobile]" placeholder="请输入财务联系人手机" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="pt1">
						<div class="form-group form-check">
						    <input type="checkbox" class="form-check-input" id="protocol" name="agree" value="yes" />
						    <label class="form-check-label text-secondary fs12" for="protocol">我已阅读并同意<a href="/protocol/pos">《POS门店合作协议》</a></label>
						  </div>
						<button class="btn btn-primary">资料确认无误，立即提交</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	{include file="pc/v1/bottom.tpl"}
{include file="footer.tpl"}