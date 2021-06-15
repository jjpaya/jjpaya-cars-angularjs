<?php
	class ApiAuthInfoController extends ApiRestController {
		public function handle_get() : bool {
			$usr = MwSessionLoader::get_user();
			
			if (!$usr) {
				echo json_encode([
					'stype' => 'none'
				]);
				
				return true;
			}
			
			$usr = $usr->get_fields();

			$am = AuthModel::get_instance();
			$jwt = new JWT(Config::get_jwt_secret());
			$exptime = time() + 60 * 60 * 24 * 7;
			
			$stype = MwSessionLoader::get_sess_type();
			
			$sdata = [
				'uid' => $usr['uid'],
				'username' => $usr['username'],
				'admin' => $usr['is_admin'],
				'img' => $usr['img'],
				'stype' => $stype,
				'exp' => $exptime
			];
			
			switch ($stype) {
				case 'local':
					$linfo = $am->get_local_acct_info($usr['uid']) ?? [];
					$sdata['email_verified'] = $linfo['email_verified'] ?? null;
					break;
			}
			
			$udatapload = $jwt->encode($sdata);
			
			echo json_encode([
				'stype' => $stype,
				'data' => $udatapload
			]);
			
			return true;
		}
	}
?>