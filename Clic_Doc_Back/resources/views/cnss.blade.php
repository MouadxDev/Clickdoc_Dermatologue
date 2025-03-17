<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>CNSS</title>
    <style>
		@page{size: A5 }
		.sautdepage
		{
			page-break-before: always;
		}
	</style>
</head>
<body >
    <div class="w-full h-full bg-white page p-4 ">
		<div>
			<div class="bg-white">
				<img src="/images_cnss/header.jpg">
			</div>
			<img src="/images_cnss/part1.jpg">
			<div class="mx-8 border-black border-2">
				<table class="w-full table font-semibold ">
					<tr>
						<td >
							Nom et prénom :
						</td>
						<td class="text-center">
							{{$patient->name}} {{$patient->surname}}
						</td>
						<td  class='text-right'>
							: الاسم العائلي والشخصي
						</td>
					</tr>
					<tr>
						<td>
							N° Immatriculation :
						</td>
						<td class="text-center">
							@if(isset($patient->coverage_number)) 
								{{$patient->coverage_number}}
							@else
								I _ I _ I _ I _ I _ I _ I _ I _ I
							@endif
						</td>
						<td class='text-right'>
							: رقم التسجیل
						</td>
					</tr>
					<tr>
						<td>
							N° CIN :
						</td>
						<td class="text-center uppercase">
							@if(isset($patient->CIN))
								{{$patient->CIN}}
							@else
								I _ I _ I _ I _ I _ I _ I _ I _ I
							@endif
						</td>
						<td class='text-right'>
							: رقم بطاقة التعریف الوطنیة 
						</td>
					</tr>
					<tr>
						<td>
							Lien de parenté du bénéficiaire avec l'assuré(e) :
						</td>
						<td class="text-center">
							<div class="grid grid-cols-2">
								<div class="grid grid-cols-5">
									<div class="text-right col-span-2">
										Enfant
									</div>
									<div class="text-center">
										<img src="/images_cnss/box.jpg" class="w-8 h-8 mx-auto">
									</div>
									<div class="text-left col-span-2">
										ابن
									</div>
								</div>
								<div class="grid grid-cols-5">
									<div class="text-right col-span-2">
										Conjoint
									</div>
									<div class="text-center">
										<img src="/images_cnss/box.jpg" class="w-8 h-8 mx-auto">
									</div>
									<div class="text-left col-span-2">
										زوج
									</div>
								</div>
							</div>
						</td>
						<td class='text-right'>
							: علاقة القرابة بین المستفید والمؤمن لھ (لھا)  
						</td>
					</tr>
					<tr>
						<td>
							Adresse :
						</td>
						<td class="text-center">
							.......................................................................................................................................
						</td>
						<td class='text-right'>
							: العنوان  
						</td>
					</tr>
					<tr>
						<td>
							Montant des frais :
						</td>
						<td class="text-center">
							<div class="grid grid-cols-5">
									<div class="text-right ">
										درھم
									</div>
									<div class="text-center col-span-3">
										{{$facture->amount}}
									</div>
									<div class="text-left">
										DHs
									</div>
								</div>
						</td>
						<td class='text-right'>
							: مبلغ المصاریف  
						</td>
					</tr>
					<tr>
						<td>
							Nombre de pièces jointes :
						</td>
						<td class="text-center">
							<img src="/images_cnss/box.jpg" class="w-10 h-10 mx-auto">
						</td>
						<td class='text-right'>
							: عدد الوثائق المرفقة  
						</td>
					</tr>
				</table>
			</div>
			<img src="/images_cnss/part2.jpg">
			<div class="mx-8 border-black border-2">
				<table class="w-full table font-semibold">
					<tr>
						<td width="30%">
							Nom et prénom :
						</td>
						<td class="text-center">
							{{$patient->name}} {{$patient->surname}}
						</td>
						<td width="30%" class='text-right'>
							: الاسم العائلي والشخصي
						</td>
					</tr>
					
					<tr>
						<td>
							Date de naissance :
						</td>
						<td class="text-center">
							@if(isset($patient->date_of_birth))
								{{$patient->date_of_birth}}
							@else
								I _ I _ I  &nbsp;&nbsp; I _ I _ I &nbsp;&nbsp; I _ I _ I_ I _ I
							@endif
						</td>
						<td class='text-right'>
							: تاریخ الازدیاد 
						</td>
					</tr>
					<tr>
						<td>
							N° CIN :
						</td>
						<td class="text-center uppercase">
							@if(isset($patient->CIN))
								{{$patient->CIN}}
							@else
								I _ I _ I _ I _ I _ I _ I _ I _ I
							@endif
						</td>
						<td class='text-right'>
							: رقم بطاقة التعریف الوطنیة 
						</td>
					</tr>
					<tr>
						<td>
							Genre :
						</td>
						<td class="text-center">
							@if($patient->sex=='M')
								ذكر - M
							@else
								أنثى - F
							@endif
						</td>
						<td class='text-right'>
							: الجنس  
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class='p-2'>
			<img src="/images_cnss/part3.jpg">
		</div>
		<span class="sautdepage" /> 
		<div class="w-full h-full">
			<img src="/CNSS2.jpg">
		</div>
		<span class="sautdepage" /> 
		<div class="w-full h-full">
			<img src="/CNSS3.jpg">
		</div>
		<span class="sautdepage" /> 
		<div class="w-full h-full">
			<img src="/CNSS4.jpg">
		</div>

	</div>
</body>
</html>