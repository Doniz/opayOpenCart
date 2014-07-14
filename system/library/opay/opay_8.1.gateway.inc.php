<?php

   
   /**
   * Žemiausia galima PHP versija 5.0.1
   */

   ////
   // KAIP PRADĖTI NAUDOTIS OpayGateway KLASE 
   //
       
    /** 
    *   1) Šį (vienintelį) failą reikia įtraukti į jūsų programos scenarijų. Po ko jums taps pasiekiama klasė pavadinimu OpayGateway. 
    *   Kiti reikiami failai bus įtraukti automatiškai. 
    * 
    * 
    *   2) Šalia šio failo turi būti patalpinti dar du failai:
    *       opay_8.1.gateway.class.v<failo_versijos_numeris>.php        -  Tai yra komunikavimą su Opay pagal standartą "opay_8.1" valdančios klasės OpayGateway failas.
    *                                                                      Kai OPAY atnaujina šį failą, pasikeičia failo versijos numeris. Failo versijos numeris neturi nieko bendro su standarto pasikeitimu.
    *                                                                      Tokiu būdu Opay pasilieka galimybę ateityje atlikti tam tikrų algoritmų patobulinimus, galimų klaidų ištaisymus.
    *       opay_8.1.gateway.core.interface.class.php                   -  Tai yra komunikavimo su Opay pagal standartą "opay_8.1" pagrindinius metodus (funkcijas) apibrėžiantis interfeisas. 
    *                                                                      Čia paminėti metodai skirti naudojimui prekybinikų sistemose. Šio interfeiso funkcijas įgyvendina OpayGateway klasė. Interfeisas naudojamas tam, kad 
    *                                                                      prekybininkai, keisdami opay_8.1.gateway.class.v<klasės_versija>.php failą į naujesnės versijos failą, būtų užtikrinti, 
    *                                                                      jog jų programiniuose scenarijuose naudojami šiame interfeise aprašyti metodai nesikeičia.         
    * 
    * 
    *    3) Turite nurodyti failo opay_8.1.gateway.class.v<failo_versijos_numeris>.php pavadinimą dalyje KONFIGŪRACIJA. Tai  skirta tam, kad patalpinę naujesnę šio failo versiją, nurodytumėt kurį iš failų naudoti.
    *       Tai darykite keisdami kintamojo $opayGatewayClassFileName reikšmę
    * 
    */


   ////
   // KONFIGŪRACIJA 
   //
    
    /**
    *   Šio kintamojo reikšmė turi atspindėti failo pavadinimą, kuriame yra aprašyta OpayGateway. 
    */

    $opayGatewayClassFileName = 'opay_8.1.gateway.class.v1.2.0.php'; 


    ////
    // ŽEMIAU NIEKO NEKEISI
    //
    
    require_once dirname(__FILE__).'/'.$opayGatewayClassFileName;
    
?>