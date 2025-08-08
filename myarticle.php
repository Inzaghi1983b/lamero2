<?php
require_once "Article.php";

class MyArticle extends Article
{
//  protected function doPepare($starttime, $max_time)
//  {
  //Update auf Header wenn Variante einen Update hat
  //todo: dass muss übersteuerbar sein
  //$db->exec("update cmt_Artikel set update_to_shop = 1, has_error = 0 where exists(select 1 from cmt_ArtikelVarianten where cmt_ArtikelVarianten.comatic_id = cmt_Artikel.comatic_id AND exists(select 1 from cmt_Artikel where cmt_Artikel.update_to_shop = 1 and cmt_Artikel.comatic_id = cmt_ArtikelVarianten.comatic2_id ))");
////    echo '<pre>doPepare($starttime, $max_time)</pre>';
//  }
//  public function buildInsertArtikelSelect()
//  {
//    return "select cmt_ArtikelFremdsprache.languagecode,cAFL.product_id as langproductid, cmt_ArtikelFremdsprache.bezeichnung as transbezeichnung, cmt_ArtikelFremdsprache.beschreibung as transbeschreibung, cmt_ArtikelFremdsprache.kurzbeschreibung as transkurzbeschreibung  , cmt_Artikel.* from cmt_Artikel
//    left join cmt_ArtikelFremdsprache on cmt_ArtikelFremdsprache.artikel_id = cmt_Artikel.id
//    left join cmt_ArtikelFremdspracheLast cAFL on cAFL.artikel_id = cmt_Artikel.id ";
//  }
//  public function buildIndexInsertArtikelSelect()
//  {
//    return "select IFNULL(Sum(1),0) As Itm from cmt_Artikel
//    left join cmt_ArtikelFremdsprache on cmt_ArtikelFremdsprache.artikel_id = cmt_Artikel.id
//    left join cmt_ArtikelFremdspracheLast cAFL on cAFL.artikel_id = cmt_Artikel.id ";
//  }

  public function buildInsertArtikelWhere()
  {
    return "where (product_id is null or product_id = '') and COALESCE(SKU,'') > '' and update_to_shop = 1 and has_error = 0 and not exists(select 1 from cmt_ArtikelVarianten where comatic2_id = cmt_Artikel.comatic_id)";
  }
//  public function buildUpdateArtikelSelect()
//  {
//    return "select cAFL.languagecode,cAFL.product_id as langproductid , cAFL.bezeichnung as transbezeichnung, cAFL.beschreibung as transbeschreibung, cAFL.kurzbeschreibung as transkurzbeschreibung , cmt_Artikel.* from cmt_Artikel
//    left join cmt_ArtikelFremdspracheLast cAFL on cAFL.artikel_id = cmt_Artikel.id ";
//  }
//
//  public function buildIndexUpdateArtikelSelect()
//  {
//    return "select IFNULL(Sum(1),0) As Itm from cmt_Artikel
//    left join cmt_ArtikelFremdspracheLast cAFL on cAFL.artikel_id = cmt_Artikel.id ";
//  }
//
  public function buildUpdateArtikelWhere()
  {
    return "where update_to_shop = 1 and product_id <> '' and product_id is not null and COALESCE(SKU,'') > '' and base_comatic_id = 0 and has_error = 0 and not exists(select 1 from cmt_ArtikelVarianten where comatic2_id = cmt_Artikel.comatic_id)";
  }

  protected function buildNewProductData($row)
  {

//    $api = getClient();
    $comaticId = $row['comatic_id'];
    $taxstatus = "taxable";
    $price = floatval($row['price']);
    $bezeichnung = $row['bezeichnung'];
    $beschreibung = nl2br($row['beschreibung']);
    $kurzbeschreibung = nl2br($row['kurzbeschreibung']);

    //wenn der Artikel varianten hat, dann ist es kein Simpel
    $type = 'simple';
    if ($this->isVariante($comaticId)) {
      $type = 'variable';
    }

    $isActiv = boolval($row['aktiv']);
    if ($isActiv) {
      $status = 'publish';
      $catalog_visibility = 'visible';
    } else {
      $status = 'private';
      $catalog_visibility = 'hidden';
    }

    $newProductData = [
      'name' => $bezeichnung,
      'title' => $bezeichnung,
      'type' => $type,
      'description' => $beschreibung,
      'short_description' => $kurzbeschreibung,
      'regular_price' => strval($price),
      'sku' => $row['SKU'],
      'status' => $status,
      'catalog_visibility' => $catalog_visibility
    ];
    return $newProductData;
  }
//  protected function doAfterCreateProduct($rowArt, $newProductData, $productId)
//  {
//    //Zusätzliches nach dem erstellen eines neuen Artikels im Shop
////    echo '<pre>protected function doAfterCreateProduct(rowArt = ' . print_r($rowArt, true) . ', newProductData = ' . print_r($newProductData, true) . ')</pre>';
//    $db = getDatabase();
//    $lang = $rowArt['languagecode'];
//    $artikelid = $rowArt['id'];
//    $checkQuery = "select * from cmt_ArtikelFremdspracheLast where artikel_id = " . $artikelid . " and languagecode = '" . $lang . "' and product_id =  " . $productId;
////    echo '<pre>checkQuery: ' . print_r($checkQuery, true) . '</pre>';
//    $dbArtFremdLast = $db->prepare($checkQuery);
//    $dbArtFremdLast->execute();
//    $hits = $dbArtFremdLast->rowCount();
////      echo '<pre> hat? ' . print_r($hits, true) . '</pre>';
//    if ($hits <= 0) {
//      $query = "select * from cmt_ArtikelFremdsprache where artikel_id = " . $artikelid . " and languagecode ='" . $lang . "'";
////      echo '<pre> hat? ' . print_r($query, true) . '</pre>';
//      $fremdsprache = $db->query($query)->fetch();
////      echo '<pre> hat? ' . print_r($fremdsprache, true) . '</pre>';
//      $insertQuery = "insert into cmt_ArtikelFremdspracheLast (artikel_id,
//        languagecode,
//        product_id,
//        bezeichnung,
//        beschreibung,
//        kurzbeschreibung,
//        anmerkung,
//        iststandardsprache,
//        beschreibung3,
//        beschreibung4,
//        beschreibung5,
//        beschreibung6
//        ) values (:artikel_id,
//        :languagecode,
//        :product_id,
//        :bezeichnung,
//        :beschreibung,
//        :kurzbeschreibung,
//        :anmerkung,
//        :iststandardsprache,
//        :beschreibung3,
//        :beschreibung4,
//        :beschreibung5,
//        :beschreibung6
//        )  ";
////      echo '<pre>insertQuery: ' . print_r($insertQuery, true) . '</pre>';
//      $stmt_last_Insert = $db->prepare($insertQuery);
//      $stmt_last_Insert->bindValue(':artikel_id', $artikelid);
//      $stmt_last_Insert->bindValue(':languagecode', $lang);
//      $stmt_last_Insert->bindValue(':product_id', $productId);
//      $stmt_last_Insert->bindValue(':bezeichnung', $fremdsprache['bezeichnung']);
//      $stmt_last_Insert->bindValue(':beschreibung', $fremdsprache['beschreibung']);
//      $stmt_last_Insert->bindValue(':kurzbeschreibung', $fremdsprache['kurzbeschreibung']);
//      $stmt_last_Insert->bindValue(':anmerkung', $fremdsprache['anmerkung']);
//      $stmt_last_Insert->bindValue(':iststandardsprache', boolval($fremdsprache['iststandardsprache']), PDO::PARAM_BOOL);
//      $stmt_last_Insert->bindValue(':beschreibung3', $fremdsprache['beschreibung3']);
//      $stmt_last_Insert->bindValue(':beschreibung4', $fremdsprache['beschreibung4']);
//      $stmt_last_Insert->bindValue(':beschreibung5', $fremdsprache['beschreibung5']);
//      $stmt_last_Insert->bindValue(':beschreibung6', $fremdsprache['beschreibung6']);
//      $stmt_last_Insert->execute();
////      $db->exec($insertQuery);
//    }
//  }

  protected function buildNewVariantData($rowVar, $variantenAttribut, $price)
  {
    $data = [
      'regular_price' => strval($rowVar['preis']),
      'stock_quantity' => $rowVar['lagerstand'],
//        'image' => [
//          'id' => 423
//        ],
      'attributes' => [
        [
          'id' => $variantenAttribut->id,
          'option' => $rowVar['variante']
        ]
      ]
    ];
    return $data;
  }

  protected function buildNewVariantDataMultiAttributes($variant, $price)
  {
//    echo '<pre>myArticle.buildNewVariantDataMultiAttributes: ' . $price . ' Var: ' . print_r($variant, true) . ' </pre>';
    $attributes = array();
    foreach ($variant['atts'] as $option) {
//      echo '<pre>myArticle.buildNewVariantDataMultiAttributes Option: ' . print_r($option, true) . ' </pre>';
      $attId = intval($option['id']);
//      echo '<pre>myArticle.buildNewVariantDataMultiAttributes Option: ' . print_r($option['option'], true) . ' </pre>';
      $optName = strval($option['option']->product_attribute_term->name);
      $attributes[] = array(
        'id' => $attId,
        'option' => $optName);
//      $attributes[] = $option;
//
    }
    $data = [
      'regular_price' => strval($variant['preis']),
      'stock_quantity' => $variant['lagerstand'],
      'attributes' => $attributes
    ];
    return $data;

  }


  protected function buildUpdateProductData($row, $coverId)
  {
    $logTag = "UpdProdData";
    $articleId = $row['artikel_id'];
    $productId = $row['product_id'];
    $comaticId = $row['comatic_id'];

    $price = floatval($row['price']);
    $bezeichnung = $row['bezeichnung'];
    $beschreibung = nl2br($row['beschreibung']);
    $kurzbeschreibung = nl2br($row['kurzbeschreibung']);

    //wenn der Artikel varianten hat, dann ist es kein Simpel
    $type = 'simple';
    $managingstock = true;
    if ($this->isVariante($comaticId)) {
      $type = 'variable';
      $managingstock = false;
    }

//    echo '<pre>Type: ' . print_r($type, true) . '</pre>';
//      echo '<pre>Aktiv: ' . print_r($isActiv, true) . '</pre>';

    $isActiv = boolval($row['aktiv']);
    if ($isActiv) {
      $status = 'publish';
      $catalog_visibility = 'visible';
    } else {
      $status = 'private';
      $catalog_visibility = 'hidden';
    }


    $stockquantity = intval($row['lagerstand']);
    if (intval($row['lagerstand']) > 0) {
      $stockstatus = "instock";
    } else {
      $stockstatus = "outofstock";
    }
    //27.07.2022 markus Backorder management
    $backorders = "no";
    $backorders_allowed = null;
//    echo '<pre>customerfield01: ' . print_r($row['customerfield01'], true) . '</pre>';
    switch ($row['customerfield01']) {
      case "Erlauben":
        $backorders = "yes";
        $backorders_allowed = 1;
        break;
      case "Erlauben, aber Kunde benachrichtigen":
        $backorders = "notify";
        $backorders_allowed = 1;
        break;
      case "Nicht erlauben":
        //bleibt wie der Standard
        break;
      default:
        $msg = "Artikel: " . $row['SKU'] . " keine Backorder logik für '" . $row['customerfield01'] . "' bekannt.";
        logWarning($logTag, $msg);
    }

    $updateProductData = [
      'name' => $bezeichnung,
      'title' => $bezeichnung,
      'type' => $type,
      'description' => $beschreibung,
      'short_description' => $kurzbeschreibung,
      'regular_price' => strval($price),
      'sku' => $row['SKU'],
      'manage_stock' => $managingstock,
      'stock_quantity' => $stockquantity,
      'stock_status' => $stockstatus,
      'status' => $status,
      'catalog_visibility' => $catalog_visibility,
      'backorders' => $backorders,
      'backorders_allowed' => $backorders_allowed
    ];
//    echo '<pre>Row: ' . print_r($updateProductData, true) . '</pre>';
    return $updateProductData;

  }

  protected function buildUpdateVariantData($rowVar, $variantenAttribut, $price)
  {
    $data = [
      'regular_price' => strval($rowVar['preis']),
      'stock_quantity' => $rowVar['lagerstand'],
//        'image' => [
//          'id' => 423
//        ],
    ];
    return $data;
  }
//  protected function extendAttributes($id, $attributes, $rowArt)
//  {
//    echo '<pre>extendAttributes($id = ' . $id . ', $attributes= ' . print_r($attributes, true) . ', $rowArt = ' . print_r($rowArt, true) . ')</pre>';
//    return $attributes;
//  }

  protected function buildLoadACFAttributesQuery($comaticId)
  {
    return "select cmt_ArtikelAttributes.*, cO.shopid, at.shopid as shop_attribute_id, at.type, cO.bezeichnung as option_name
    from cmt_ArtikelAttributes inner join cmt_Attribute at on at.id = cmt_ArtikelAttributes.attribute_id left outer join cmt_Optionen cO on cmt_ArtikelAttributes.option_id = cO.id
                                                           where at.deleted = false and (cO.deleted is null or cO.deleted = false) and at.isacf = true and at.shopid <> 'datasheet' and cmt_ArtikelAttributes.comatic_id =" . $comaticId;
  }

  protected function addDocumentToProduct($productId, $art, $document)
  {
//    echo '<pre>Dokument ans Datenblatt, productid ' .$productId. ': ' . print_r($document, true) . '</pre>';
    $db = getDatabase();
    $api = getWCACFClient();
    //lesen des Attributs
    $getAttributQuery = "select * from cmt_Attribute where code ='datasheet'";
    foreach ($db->query($getAttributQuery) as $rowAtt) {
//      echo '<pre>Dokument ans Datenblatt, Attribut: ' . print_r($rowAtt, true) . '</pre>';
      $updateProductData = array('datasheet' => intval($document['mediaid']));
      break;
    }
//    echo "<pre>addDocumentToProduct: " . print_r($updateProductData, true) . "</pre>";
    $api->attributes->update($productId, $updateProductData);
  }

  protected function loadVarianten($comaticId, $productId, $variantenAttributeName, $languagecode)
  {
    $logTag = "LoadVar";
//    echo '<pre>myArticle.loadVarianten comaticId ' . $comaticId . ', productId ' . $productId . ', variantenAttributeName ' . $variantenAttributeName . ' </pre>';
    $api = getWCClient();
    $db = getDatabase();
    try {
      //lesen der Varianten Optionen
      $query = "select cA.id as VarianteId , cAVL.link_id as LinkId , cmt_ArtikelVarianten.* from cmt_ArtikelVarianten
            join cmt_Artikel cA on cmt_ArtikelVarianten.comatic2_id = cA.comatic_id
            left join cmt_ArtikelVariantenLast cAVL on cmt_ArtikelVarianten.comatic_id = cAVL.comatic_id and cmt_ArtikelVarianten.variante = cAVL.variante
         where cmt_ArtikelVarianten.comatic_id = $comaticId order by gewichtung";
//    echo '<pre>query: ' . print_r($query, true) . '</pre>';
      $variantenAtt = array();
      $varianten = array();
      $attributes = array();
      foreach ($db->query($query) as $rowVar) {
//      echo '<pre>VAriante: ' . print_r($rowVar, true) . '</pre>';
        //dann die beiden ersten Optionen
        $optQuery = "select cO.shopid as OptId, cmt_Attribute.shopid, cmt_ArtikelAttributes.* from cmt_ArtikelAttributes
            join cmt_Attribute on cmt_ArtikelAttributes.attribute_id = cmt_Attribute.id
            join cmt_Optionen cO on cmt_ArtikelAttributes.option_id = cO.id
            where comatic_id = " . $rowVar['VarianteId'] . " order by sortorder limit 2";
//        echo '<pre>query: ' . print_r($optQuery, true) . '</pre>';
        $attributes = array();
        foreach ($db->query($optQuery) as $rowOpt) {
//        echo '<pre>Opt: ' . print_r($rowOpt, true) . '</pre>';
          //attribut holen
          $variantenAttribut = $api->attributes->get(intval($rowOpt['shopid']));
//          echo '<pre>variantenAttribut: ' . print_r($variantenAttribut, true) . '</pre>';
          //prüfen ob der schon drin ist.
          $exsts = null;
          foreach ($attributes as $item) {
//            echo '<pre>$item: ' . print_r($item, true) . '</pre>';
            if ($item['id'] == $variantenAttribut->product_attribute->id) {
              $exsts = $item;
              break;
            }
          }
          if ($exsts == null) {
            $attOpt = $api->attribute_terms->get($variantenAttribut->product_attribute->id, $rowOpt['OptId']);
//            echo '<pre>$attOpt: ' . print_r($attOpt, true) . '</pre>';
            $variantenAtt [] = array(
              'id' => $variantenAttribut->product_attribute->id,
              'slug' => $variantenAttribut->product_attribute->slug,
              'option' => $attOpt);
            $variantenption = $api->attribute_terms->get($variantenAttribut->product_attribute->id);
//          echo '<pre>variantenption: ' . print_r($variantenption->product_attribute_terms, true) . '</pre>';
            $attOptins = array();
            foreach ($variantenption->product_attribute_terms as $attribute_term) {
//            echo '<pre>attribute_term: ' . print_r($attribute_term, true) . '</pre>';
              $attOptins[] = $attribute_term->name;
            }
//          $options[] = array(
//            'attribute' => $variantenAttribut->product_attribute,
//            'option' => $variantenption->product_attribute_term,
//            'attribute_id' => $rowOpt['shopid'],
//            'option_id' => $rowOpt['OptId'],
//            'wert' => $rowOpt['wert']);
//          $options[] = array(
//            'id' => $variantenAttribut->product_attribute->id,
//            'option' => $attOptins);
            $attributes[] = array(
              'id' => $variantenAttribut->product_attribute->id,
              'variation' => true,
              'visible' => true,
              'options' => $attOptins);
          }
        }
//        echo '<pre>Variante Row: ' . print_r($rowVar, true) . '</pre>';
        $varianten[] = array(
          'variante' => $rowVar['variante'],
          'linkid' => $rowVar['LinkId'],
          'comatic_id' => $rowVar['comatic_id'],
          'comatic2_id' => $rowVar['comatic2_id'],
          'preis' => $rowVar['preis'],
          'lagerstand' => $rowVar['lagerstand'],
          'atts' => $variantenAtt);
      }
//    echo '<pre>optionen: ' . print_r($varianten, true) . '</pre>';
      //return $varianten;
      $response = array(
        'varianten' => $varianten,
        'attributes' => $attributes);
//      echo '<pre>res: ' . print_r($response, true) . '</pre>';
      return $response;
    } catch (Exception $updateBug) {
      $msg = $updateBug->getMessage();
      //echo '<pre>Set Error ' . $productId . ' Fehler :';      print_r($msg);      echo '</pre>';
      logError($logTag, $msg);
      throw $updateBug;
    }
  }

  protected function extendVarianten(array $updateProductData, array $varianten)
  {
//    echo '<pre>update article, Varianten: ' . print_r($varianten['attributes'], true) . '</pre>';
    foreach ($varianten['attributes'] as $attribute) {
      $updateProductData['attributes'][] = $attribute;
    }
    return $updateProductData;

  }

  protected function addVarianten($comaticId, $productId, $variantenAttributeName, $price, $variants = null)
  {
//    echo '<pre>myArticle.addVarianten comaticId ' . $comaticId . ', productId ' . $productId . ', variantenAttributeName ' . $variantenAttributeName . ' </pre>';
    $api = getWCClient();
    $db = getDatabase();

    if ($variants == null) {
      throw new Exception("noch mal laden");
    }
//    echo '<pre>myArticle.addVarianten variants: ' . print_r($variants, true) . ' </pre>';
    // lesen des Artikels im Shop
    $shopArticle = $api->products->get($productId);
//    echo '<pre>myArticle.addVarianten shopArticle->variations: ' . print_r($shopArticle->variations, true) . ' </pre>';
    foreach ($variants['varianten'] as $variant) {
//      echo '<pre>myArticle.addVarianten Variante: ' . print_r($variant, true) . ' </pre>';

      $woovariant = null;
      if (isset($shopArticle->variations) && count($shopArticle->variations) > 0) {
        foreach ($shopArticle->variations as $variation) {
//          echo '<pre>myArticle.addVarianten variation: ' . print_r($variation, true) . ' </pre>';
          if ($variation == $variant['linkid']) {
            $woovariant = $api->products->get($variation);;
//            echo '<pre>myArticle.addVarianten variation: ' . print_r($woovariant, true) . ' </pre>';
            break;
          }
        }
      }
      if ($woovariant == null) {
        $data = $this->buildNewVariantDataMultiAttributes($variant, $price);
        $createdVariant = $api->products->createVariant($productId, $data);
        $variantId = $createdVariant->id;
      } else {
        $data = $this->buildUpdateVariantData($variant, null, $price);
////        echo '<pre>data: ' . print_r($data, true) . '</pre>';

        if (is_a($variant, 'stdClass')) {
          $variantId = $variant->id;
          $api->products->updateVariant($productId, $variant->id, $data);
        } else {
          $variantId = intval($variant['linkid']);
          $api->products->updateVariant($productId, $variant['linkid'], $data);
        }
      }
//      echo '<pre>variantId: ' . print_r($variantId, true) . '</pre>';
      $checkQuery = "select * from cmt_ArtikelVariantenLast where comatic_id = " . $variant['comatic_id'] . " and link_id = " . $variantId;
//      echo '<pre>checkQuery: ' . print_r($checkQuery, true) . '</pre>';
      $dbArtVartLast = $db->prepare($checkQuery);
      $dbArtVartLast->execute();
      $hits = $dbArtVartLast->rowCount();
//      echo '<pre> hat? ' . print_r($hits, true) . '</pre>';
      if ($hits <= 0) {
        $insertQuery = "insert into cmt_ArtikelVariantenLast (comatic_id,  variante, link_id) values (" . $variant['comatic_id'] . ",'" . $variant['variante'] . "', '" . $variantId . "') on duplicate key update link_id = " . $variantId;
//      echo '<pre>insertQuery: ' . print_r($insertQuery, true) . '</pre>';
        $db->exec($insertQuery);
      }
      $updQuery = "update cmt_ArtikelVarianten set has_error = false, product_id = " . $variantId . " where comatic_id = " . $comaticId . " and variante = '" . $variant['variante'] . "';";
//      echo '<pre>variant updQuery: ' . print_r($updQuery, true) . '</pre>';
      $db->exec($updQuery);
      $updQuery = "update cmt_Artikel set has_error = false,update_to_shop = 0, product_id = " . $variantId . " where comatic_id = " . $variant['comatic2_id'];
//      echo '<pre>variant updQuery: ' . print_r($updQuery, true) . '</pre>';
      $db->exec($updQuery);

    }
  }
}
