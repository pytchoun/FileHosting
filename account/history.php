<?php
include '../assets/include/head.php';
if (!isset($_SESSION["account"]["email"])) {
  header('Location: ../login.php');
}
$db = connectDb();
// Définition du nombre de factures à afficher
$billLimite = 10;

$page = 1;
$begin = ($page - 1) * $billLimite;

// Obtenir les factures de l'utilisateur
$query = $db->prepare("SELECT * FROM BILL INNER JOIN SUBSCRIPTION ON BILL.id_subscription=SUBSCRIPTION.id WHERE id_user=:id_user ORDER BY BILL.id DESC LIMIT $billLimite OFFSET $begin");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->execute();
$result = $query->fetchAll();

// Obtenir le nombre de factures de l'utilisateur
$query = $db->prepare("SELECT COUNT(*) FROM BILL WHERE id_user=:id_user");
$query->bindParam(':id_user', $_SESSION["account"]["id"]);
$query->execute();
$billNumber = $query->fetch();

$NumberOfPages = ceil($billNumber[0] / $billLimite);
?>
<body>
  <section>
    <div class="container">
      <div class="row my-5">
        <div class="col-md-12">
          <h1 class="text-center" style="background-color:#84b749; color:white; padding:7px 0px; border-radius:5px;">Mon historique des paiements</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group">
              <label for="historical-count">Afficher</label>
              <select id="historical-count" name="bill-number" onchange="showBill(this.value, 1)">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="-1">Tous</option>
              </select> factures
            </div>
          </form>
          <div id="show-bill">
            <?php
            if (empty($result)) { ?>
              <p class="text-center">
                Vous n'avez aucune factures.
              </p>
            <?php } elseif (!empty($result)) { ?>
              <div class="table-responsive">
                <table class="table table-borderless table-filehosting table-striped-filehosting table-hover-filehosting text-center">
                  <thead class="thead-filehosting">
                    <tr>
                      <th>Facture #</th>
                      <th>Forfait</th>
                      <th>Date de facturation</th>
                      <th>Prix</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($result as $row => $bill) {
                      $bill['billing_date'] = strtotime($bill['billing_date']);
                      $bill['billing_date'] = date("d/m/Y", $bill['billing_date']);
                      ?>
                      <tr>
                        <td><?php echo $bill['0']; ?></td>
                        <td><?php echo $bill['name']; ?></td>
                        <td><?php echo $bill['billing_date']; ?></td>
                        <td><?php echo $bill['price']." €"; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
              <p>
                <?php
                if ($billNumber[0] > 1) {
                  $bill = "factures";
                } else {
                  $bill = "facture";
                } ?>
                Vous avez un total de <?php echo $billNumber[0]." ".$bill; ?>.
              </p>
            <?php } ?>
            <div class="row">
              <div class="col-md-12">
                <nav aria-label="Page navigation">
                  <ul class="pagination justify-content-center">
                    <?php
                    if($page > 1) { ?>
                      <li class="page-item" value="1" onclick="showBill(<?php echo $billLimite; ?>, this.value)"><a class="page-link pagination-first">Début</a></li>
                      <li class="page-item" value="<?php echo $page - 1; ?>" onclick="showBill(<?php echo $billLimite; ?>, this.value)"><a class="page-link pagination-previous"><<</a></li>
                    <?php }
                    for($i=1; $i<=$NumberOfPages; $i++) { ?>
                      <?php if ($page == $i) { ?>
                        <li class="page-item" value="<?php echo $i; ?>" onclick="showBill(<?php echo $billLimite; ?>, this.value)"><span class="page-link pagination-page-link-current"><?php echo $i; ?><span class="sr-only">(current)</span></span>
                      <?php } elseif ($i<=$NumberOfPages) { ?>
                        <li class="page-item" value="<?php echo $i; ?>" onclick="showBill(<?php echo $billLimite; ?>, this.value)"><a class="page-link pagination-page-link"><?php echo $i; ?></a></li>
                      <?php } ?>
                    <?php }
                    if($page < $NumberOfPages) { ?>
                      <li class="page-item" value="<?php echo $page + 1; ?>" onclick="showBill(<?php echo $billLimite; ?>, this.value)"><a class="page-link pagination-next">>></a></li>
                      <li class="page-item" value="<?php echo $NumberOfPages; ?>" onclick="showBill(<?php echo $billLimite; ?>, this.value)"><a class="page-link pagination-last">Fin</a></li>
                    <?php } ?>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2">
              <p>
                <a class="btn btn-lg btn-outline-cyan btn-block" href="../account/index.php" style="margin-top:10px;"><i class="fas fa-chevron-left"></i> Retour</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php
  include '../assets/include/footer.php';
  ?>
</body>
