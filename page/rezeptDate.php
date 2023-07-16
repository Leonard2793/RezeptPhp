<?php
if(isset($_POST['month']))
{
  try
  {
    $month = $_POST['thisMonth'];
    switch($month)
    {
      case 1:
        lastMonth();
        break;
      case 2:
        thisMonth();
        break;
      case 3:
        selectMonth($_POST['dateMonth']);
        break;
    }
  }catch(Exception $e)
  {
    echo 'Error - Rezept: '.$e->getCode().': '.$e->getMessage().'<br>';
  }
}elseif(isset($_POST['date']))
{
  try{
    if(isset($_POST['date_von']) && isset($_POST['date_bis']) && empty($_POST['date_bis']))
    {
      dateVon($_POST['date_von']);
    }
    else if(isset($_POST['date_bis']) && isset($_POST['date_von']) && !empty($_POST['date_bis']))
    {
      dateBis($_POST['date_von'], $_POST['date_bis']);
    }
  }catch(Exception $e)
  {
    echo 'Error - Rezept: '.$e->getCode().': '.$e->getMessage().'<br>';
  }
}else
{
?>

<form method="post">
  <h1>Rezept</h1><br>
  <h3>Rezepte nach Bereitstellungszeitraum durchsuchen</h3><br>
  <div class="input-group mb-3">
    <span class="input-group-text" id="basic-addon1">Zeitraum von</span>
    <input type="date" class="form-control" name="date_von" aria-describedby="basic-addon1">
  </div>
  <div class="input-group mb-3">
    <span class="input-group-text" id="basic-addon1">Zeitraum bis (optional)</span>
    <input type="date" class="form-control" name="date_bis" aria-describedby="basic-addon1">
  </div>
  <div class="text-center">
    <button type="submit" class="btn btn-primary" name="date">Suche starten</button>
  </div><br>

  <h3>Oder wählen sie folgende Option aus</h3><br>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="thisMonth" value="1" id="flexRadioDefault1">
    <label class="form-check-label" for="flexRadioDefault1">
      letzer Monat
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="thisMonth" value="2" id="flexRadioDefault2" checked>
    <label class="form-check-label" for="flexRadioDefault2">
      laufender Monat
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="radio" name="thisMonth" value="3" id="flexRadioDefault3">
    <label class="form-check-label" for="flexRadioDefault3">
      <div class="row">
        <div class="col">
          <input type="number" name="dateMonth" id="" class="form-control">
        </div>
        <div class="col">
        <p style="white-space: nowrap;">Monat des laufenden Jahres angeben z.B 4</p>
        </div>
      </div>
    </label>
  </div>
  <div class="text-center">
    <button type="submit" class="btn btn-primary" name="month">Suche starten</button>
  </div>
</form>
<?php
}

function lastMonth()
{
  echo '<br><h1>Alle Ergebnisse für den letzten Monat:  </h1><hr>';

  global $con;

  $queryTitle = "SELECT 
                  rez_name as 'name',
                  zubereitung.zub_beschreibung as 'beschreibung'
                  FROM zubereitung
                  left join rezeptname
                  on rezeptname.rez_id = zubereitung.rez_id
                  WHERE DATE_FORMAT(zubereitung.zub_bereitgestellt_am, '%Y-%m') 
                  = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')";
  
  $stmt = $con->prepare($queryTitle);
  $stmt->execute();

  while($row = $stmt->fetch(PDO::FETCH_NUM))
  {
    echo '<h3>Rezept '.$row[0].':</h3> '.$row[1];
    $query = "select
              zubereitung_zutat_einheit.zubein_menge as 'Menge',
              einheit.einheit_name as 'Einheit',
              zutat.zutat_name as 'Zutat'
              from zubereitung 
              left join zubereitung_zutat_einheit
              on zubereitung.zub_id = zubereitung_zutat_einheit.zub_id
              left join zutat_einheit
              on zutat_einheit.zutat_einheit_id = zubereitung_zutat_einheit.zutat_einheit_id
              left join zutat
              on zutat.zutat_id = zutat_einheit.zutat_id
              left join einheit
              on einheit.einheit_id = zutat_einheit.einheit_id
              left join rezeptname
              on rezeptname.rez_id = zubereitung.rez_id
              WHERE DATE_FORMAT(zubereitung.zub_bereitgestellt_am, '%Y-%m') 
              = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')";
    makeTable($query);
  }
}

function thisMonth()
{
  echo '<br><h1>Alle Ergebnisse für des aktuellen Monats:  </h1><hr>';

  global $con;

  $queryTitle = "SELECT 
                  rez_name as 'name',
                  zubereitung.zub_beschreibung as 'beschreibung'
                  FROM zubereitung
                  left join rezeptname
                  on rezeptname.rez_id = zubereitung.rez_id
                  WHERE YEAR(zub_bereitgestellt_am) 
                  = YEAR(CURDATE()) AND MONTH(zub_bereitgestellt_am) 
                  = MONTH(CURDATE())";
  
  $stmt = $con->prepare($queryTitle);
  $stmt->execute();

  while($row = $stmt->fetch(PDO::FETCH_NUM))
  {
    echo '<h3>Rezept '.$row[0].':</h3> '.$row[1];
    $query = "SELECT 
              zubereitung_zutat_einheit.zubein_menge as 'Menge',
              einheit.einheit_name as 'Einheit',
              zutat.zutat_name as 'Zutat'
              from zubereitung 
              left join zubereitung_zutat_einheit
              on zubereitung.zub_id = zubereitung_zutat_einheit.zub_id
              left join zutat_einheit
              on zutat_einheit.zutat_einheit_id = zubereitung_zutat_einheit.zutat_einheit_id
              left join zutat
              on zutat.zutat_id = zutat_einheit.zutat_id
              left join einheit
              on einheit.einheit_id = zutat_einheit.einheit_id
              left join rezeptname
              on rezeptname.rez_id = zubereitung.rez_id
              WHERE YEAR(zub_bereitgestellt_am) 
              = YEAR(CURDATE()) AND MONTH(zub_bereitgestellt_am) 
              = MONTH(CURDATE())";
    makeTable($query);
  }
}

function selectMonth($month)
{
  echo '<br><h1>Alle Ergebnisse für des '.$month.' Monats:  </h1><hr>';

  global $con;

  $queryTitle = "SELECT 
                  rez_name as 'name',
                  zubereitung.zub_beschreibung as 'beschreibung'
                  FROM zubereitung
                  left join rezeptname
                  on rezeptname.rez_id = zubereitung.rez_id
                  WHERE YEAR(zub_bereitgestellt_am) = YEAR(CURDATE()) 
                  AND MONTH(zub_bereitgestellt_am) = ?";
  
  $stmt = $con->prepare($queryTitle);
  $stmt->execute(array($month));

  while($row = $stmt->fetch(PDO::FETCH_NUM))
  {
    echo '<h3>Rezept '.$row[0].':</h3> '.$row[1];
    $query = "SELECT
              zubereitung_zutat_einheit.zubein_menge as 'Menge',
              einheit.einheit_name as 'Einheit',
              zutat.zutat_name as 'Zutat'
              from zubereitung 
              left join zubereitung_zutat_einheit
              on zubereitung.zub_id = zubereitung_zutat_einheit.zub_id
              left join zutat_einheit
              on zutat_einheit.zutat_einheit_id = zubereitung_zutat_einheit.zutat_einheit_id
              left join zutat
              on zutat.zutat_id = zutat_einheit.zutat_id
              left join einheit
              on einheit.einheit_id = zutat_einheit.einheit_id
              left join rezeptname
              on rezeptname.rez_id = zubereitung.rez_id
              WHERE YEAR(zub_bereitgestellt_am) 
              = YEAR(CURDATE()) AND MONTH(zub_bereitgestellt_am) 
              = MONTH(CURDATE())";
    makeTablePar($query, array($month));
  }
}

function dateVon($date)
{
  echo '<br><h1>Alle Ergebnisse ab des '.$date.' Monats:  </h1><hr>';
  
  global $con;
  $array = array($date);
  $queryTitle = "SELECT 
                  rez_name as 'name',
                  zubereitung.zub_beschreibung as 'beschreibung'
                  FROM zubereitung
                  left join rezeptname
                  on rezeptname.rez_id = zubereitung.rez_id
                  where zub_bereitgestellt_am >= ?";
  
  $stmt = $con->prepare($queryTitle);
  $stmt->execute($array);

  while($row = $stmt->fetch(PDO::FETCH_NUM))
  {
    echo '<h3>Rezept '.$row[0].':</h3> '.$row[1];
    $query = "SELECT 
              zubereitung_zutat_einheit.zubein_menge as 'Menge',
              einheit.einheit_name as 'Einheit',
              zutat.zutat_name as 'Zutat'
              from zubereitung 
              left join zubereitung_zutat_einheit
              on zubereitung.zub_id = zubereitung_zutat_einheit.zub_id
              left join zutat_einheit
              on zutat_einheit.zutat_einheit_id = zubereitung_zutat_einheit.zutat_einheit_id
              left join zutat
              on zutat.zutat_id = zutat_einheit.zutat_id
              left join einheit
              on einheit.einheit_id = zutat_einheit.einheit_id
              left join rezeptname
              on rezeptname.rez_id = zubereitung.rez_id
              where zub_bereitgestellt_am >= ?";
    makeTablePar($query, array($date));
  }
}

function dateBis($datevon, $datebis)
{
  echo '<br><h1>Alle Ergebnisse zwischen den Zeitraum von '.$datevon.' und '.$datebis.':  </h1><hr>';

  global $con;

  $array1 = array($datevon, $datebis);

  $queryTitle = "SELECT
                  zubereitung.zub_id, 
                  rez_name as 'name',
                  zubereitung.zub_beschreibung as 'beschreibung'
                  FROM zubereitung
                  left join rezeptname
                  on rezeptname.rez_id = zubereitung.rez_id
                  where zub_bereitgestellt_am >= ? and zub_bereitgestellt_am <= ?";
  
  $stmt = $con->prepare($queryTitle);
  $stmt->execute($array1);

  while($row = $stmt->fetch(PDO::FETCH_NUM))
  {
    $array = array($datevon, $datebis, $row[0]);
    echo '<h3>Rezept '.$row[1].':</h3> '.$row[2];
    $query = "SELECT  
              zubereitung_zutat_einheit.zubein_menge as 'Menge',
              einheit.einheit_name as 'Einheit',
              zutat.zutat_name as 'Zutat'
              from zubereitung 
              left join zubereitung_zutat_einheit
              on zubereitung.zub_id = zubereitung_zutat_einheit.zub_id
              left join zutat_einheit
              on zutat_einheit.zutat_einheit_id = zubereitung_zutat_einheit.zutat_einheit_id
              left join zutat
              on zutat.zutat_id = zutat_einheit.zutat_id
              left join einheit
              on einheit.einheit_id = zutat_einheit.einheit_id
              left join rezeptname
              on rezeptname.rez_id = zubereitung.rez_id
              where zub_bereitgestellt_am >= ? 
              and zub_bereitgestellt_am <= ?
              and zubereitung.zub_id = ?";
    makeTablePar($query, $array);
  }
}


?>

