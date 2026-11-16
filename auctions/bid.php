<?php
session_start();require '../includes/security.php';require '../includes/db.php';
if(!isset($_SESSION['user_id'])){header('Location: ../login.php');exit;}
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('POST required');}csrf_check();
$id=filter_var($_POST['auction_id']??null,FILTER_VALIDATE_INT);$raw=(string)($_POST['bid_amount']??'');
if(!$id || $id<1){http_response_code(400);exit('Invalid auction');}
try{
 if(!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/D',$raw) || (float)$raw<=0) throw new DomainException('Enter a valid positive bid.');
 $amount=(float)$raw;$uid=(int)$_SESSION['user_id'];$conn->begin_transaction();
 $s=$conn->prepare('SELECT current_price,end_time FROM auctions WHERE id=? FOR UPDATE');$s->bind_param('i',$id);$s->execute();$a=$s->get_result()->fetch_assoc();
 if(!$a) throw new DomainException('Auction not found.');
 if(strtotime($a['end_time'])<=time()) throw new DomainException('This auction has ended.');
 if($amount<=(float)$a['current_price']) throw new DomainException('Your bid must exceed K'.number_format($a['current_price'],2).'.');
 $s=$conn->prepare('UPDATE auctions SET current_price=? WHERE id=?');$s->bind_param('di',$amount,$id);if(!$s->execute())throw new RuntimeException('Update failed');
 $s=$conn->prepare('INSERT INTO bids (auction_id,user_id,amount,created_at) VALUES (?,?,?,NOW())');$s->bind_param('iid',$id,$uid,$amount);if(!$s->execute())throw new RuntimeException('Insert failed');
 $conn->commit();$_SESSION['flash']='Bid accepted!';
}catch(Throwable $e){$conn->rollback();$_SESSION['flash']=$e instanceof DomainException?$e->getMessage():'Unable to place bid. Try again.';}
header('Location: view.php?id='.$id);exit;
