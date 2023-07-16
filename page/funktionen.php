<?php

function makeTable($query)
{
    global $con;
    try {
        $stmt = $con->prepare($query);
        $stmt->execute();

        /*Tabelle mit "dynamischer" Spaltenbezeichnung mittels meta-Daten*/
        $meta = array();

        echo '<table class="table">
            <tr>';
        $colCount = $stmt->columnCount();
        for ($i = 0; $i < $colCount; $i++) {
            $meta[] = $stmt->getColumnMeta($i);
            echo '<th>' . $meta[$i]['name'] . '</th>';
        }

        echo '</tr>';

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo '<tr>';
            foreach ($row as $r) {
                echo '<td>' . $r . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
    } catch (Exception $e) {
        echo 'Error - Ta bellen Adressen: ' . $e->getCode() . ': ' . $e->getCode() . '<br>';
    }
}

function makeTablePar($query, $executeArray = NULL)
{
    global $con;
    try {
        $stmt = $con->prepare($query);
        $stmt->execute($executeArray);

        /*Tabelle mit "dynamischer" Spaltenbezeichnung mittels meta-Daten*/
        $meta = array();

        echo '<table class="table">
            <tr>';
        $colCount = $stmt->columnCount();
        for ($i = 0; $i < $colCount; $i++) {
            $meta[] = $stmt->getColumnMeta($i);
            echo '<th>' . $meta[$i]['name'] . '</th>';
        }

        echo '</tr>';

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo '<tr>';
            foreach ($row as $r) {
                echo '<td>' . $r . '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
    } catch (Exception $e) {
        echo 'Error - Tabellen Adressen: ' . $e->getCode() . ': ' . $e->getCode() . '<br>';
    }
}

function makeStatement($query, $executeArray = NULL)
{
    global $con;

    $stmt = $con->prepare($query);
    $stmt->execute($executeArray);
    return $stmt;
}


function makeLastInsertRed($query)
{
    global $con;
    try {
        $stmt = $con->prepare($query);
        $stmt->execute();
        
        /*Tabelle mit "dynamischer" Spaltenbezeichnung mittels meta-Daten*/
        $meta = array();

        echo '<table class="table">
            <tr>';
        $colCount = $stmt->columnCount();
        for ($i = 0; $i < $colCount; $i++) {
            $meta[] = $stmt->getColumnMeta($i);
            echo '<th>' . $meta[$i]['name'] . '</th>';
        }

        echo '</tr>';

        $lastID = getLastInsertOrdID();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo '<tr>';
            if($row[0] === $lastID)
            {
                echo '<td><font color="red">' . $row[0] . '</font></td>';
                echo '<td><font color="red">' . $row[1] . '</font></td>';
            }else{
                foreach ($row as $r) {
                    echo '<td>' . $r . '</td>';
                }
            }
            echo '</tr>';
        }

        echo '</table>';
    } catch (Exception $e) {
        echo 'Error - Ta bellen Adressen: ' . $e->getCode() . ': ' . $e->getCode() . '<br>';
    }
}

function getLastInsertOrdID()
{
    $query = "select ort_id from ort order by last_insert desc limit 1";
    global $con;
    $stmt = $con->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC)["ort_id"];
}