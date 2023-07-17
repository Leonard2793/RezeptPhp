
<form method="POST">

<?php 
echo "<h1>Rezeptsuche</h1>";

if(isset($_POST['suchen']) || isset($_POST['show']))
{
  try
  {
    global $con;

    if(isset($_POST['search']))
    {
      $search = $_POST['search'];
      $query = "select rez_id as 'ID', rez_name as 'NAME' from rezeptname where rez_name like '%".$search."%'";
      
      $stmt = makeStatement($query);

      echo '<br><h3>Gesucht wurde nach '.$search.'</h3>';
      echo '<label for="list">
              Ergebnisliste der Suche:&nbsp&nbsp&nbsp
            </label>';
      echo '<select name="rez_id">';

      while($row = $stmt->fetch(PDO::FETCH_NUM))
      {
        echo '<option value="'.$row[0].'">'.$row[1];
      }
      echo '</select><br>';
      echo '<input type="submit" value="Anzeigen" name="show">';

    }
    elseif(isset($_POST['show']))
    {
      $search = $_POST['rez_id'];

      $queryName = 'select rez_name from rezeptname where rez_id = ?';

      $stmtName = makeStatement($queryName, array($search));

      $rezName = $stmtName->fetch(PDO::FETCH_ASSOC)["rez_name"];

      echo '<br><h3>Alle Ergebnisse für '.$rezName.':  </h3><hr>';

      $query = "select zubereitung.zub_id as 'Rezeptnummer',
                zubereitung.zub_beschreibung as 'beschreibung'
                from zubereitung
                where zubereitung.rez_id = ?";
              
      $stmt = makeStatement($query, array($search));

      while($row = $stmt->fetch(PDO::FETCH_NUM))
      {
        echo 'Rezeptnummer '.$row[0].': '.$row[1];
        $query2 = "select
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
                  where zubereitung.zub_id = ?";
        makeTable($query2, array($row[0]));
        echo '<br><hr>';
      }
    }

  }
  catch(Exception $e)
  {
    echo 'Error - Rezept: '.$e->getCode().': '.$e->getMessage().'<br>';
  }
}
else
{
  ?>
 
  <label for="search">
    Rezeptnamen suchen(auch Wortteil möglich): 
  </label>
  <input type="text" id="search" name="search"><br>
  <input type="submit" value="Suchen" name="suchen">

  </form>

<?php
}