<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Aristextil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>
<body style='background:#000;'>
    <div class='container mt-4'>
		<div class="card">
			<div class="card-body mb-4">
				<div class='row'>
					<div class='col-12'>
						<center>
							<img height='90px' src='<?=base_url("/serverback/assets/img/logo.png");?>'/><br/><br/>
							<?=$msg?>
							<br/>.
						</center>
						<?=$tabla?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>