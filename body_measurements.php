<?php
require_once __DIR__ . '/includes/auth.php';
requireRole('user');
$userId=(int)$_SESSION['user_id']; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verifyCsrf(); $date=trim((string)($_POST['measured_on']??date('Y-m-d'))); $valid=DateTime::createFromFormat('!Y-m-d',$date);
 $fields=['weight_kg','body_fat_percent','chest_cm','waist_cm','hips_cm']; $v=[];
 foreach($fields as $f){$raw=trim((string)($_POST[$f]??''));$v[$f]=$raw===''?null:filter_var($raw,FILTER_VALIDATE_FLOAT);}
 $notes=trim((string)($_POST['notes']??''));
 if(!$valid||$valid->format('Y-m-d')!==$date)$error='Choose a valid date.';
 elseif(!array_filter($v,fn($x)=>$x!==null))$error='Enter at least one measurement.';
 elseif($v['weight_kg']!==null&&($v['weight_kg']<=0||$v['weight_kg']>500))$error='Weight must be between 0 and 500 kg.';
 elseif($v['body_fat_percent']!==null&&($v['body_fat_percent']<0||$v['body_fat_percent']>100))$error='Body fat must be between 0 and 100%.';
 else{$q=$pdo->prepare('INSERT INTO body_measurements (user_id,measured_on,weight_kg,body_fat_percent,chest_cm,waist_cm,hips_cm,notes) VALUES (?,?,?,?,?,?,?,?)');$q->execute([$userId,$date,$v['weight_kg'],$v['body_fat_percent'],$v['chest_cm'],$v['waist_cm'],$v['hips_cm'],$notes?:null]);redirect('body_measurements.php');}
}
$q=$pdo->prepare('SELECT * FROM body_measurements WHERE user_id=? ORDER BY measured_on DESC,id DESC LIMIT 100');$q->execute([$userId]);$rows=$q->fetchAll();
$pageTitle='Body measurements'; require __DIR__.'/includes/header.php';
?>
<section class="page-heading"><div><p class="eyebrow">TRACK YOUR CHANGES</p><h1>Body measurements</h1><p>Save weight and measurements over time.</p></div></section>
<section class="panel"><h2>Add measurement</h2><?php if($error):?><div class="alert error"><?=escapeHtml($error)?></div><?php endif;?>
<form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?=escapeHtml(csrfToken())?>"><div><label>Date</label><input type="date" name="measured_on" value="<?=date('Y-m-d')?>" required></div>
<?php foreach(['weight_kg'=>'Weight (kg)','body_fat_percent'=>'Body fat (%)','chest_cm'=>'Chest (cm)','waist_cm'=>'Waist (cm)','hips_cm'=>'Hips (cm)'] as $key=>$label):?><div><label><?=escapeHtml($label)?></label><input type="number" step="0.01" min="0" name="<?=escapeHtml($key)?>"></div><?php endforeach;?>
<div class="full"><label>Notes</label><textarea name="notes" rows="2" maxlength="2000"></textarea></div><div><button class="button" type="submit">Save measurement</button></div></form></section>
<section class="panel"><h2>Measurement history</h2><?php if(!$rows):?><p>No measurements yet.</p><?php else:?><div class="table-wrap"><table><thead><tr><th>Date</th><th>Weight</th><th>Body fat</th><th>Chest</th><th>Waist</th><th>Hips</th><th>Notes</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=escapeHtml($r['measured_on'])?></td><?php foreach(['weight_kg','body_fat_percent','chest_cm','waist_cm','hips_cm'] as $f):?><td><?=$r[$f]===null?'—':escapeHtml((string)$r[$f])?></td><?php endforeach;?><td><?=escapeHtml($r['notes']?:'—')?></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></section>
<?php require __DIR__.'/includes/footer.php';?>
