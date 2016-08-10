
// var app_adminauth = false ;  // 管理開關  全域變數 var app_adminauth = false ;
// 改經由 $rootScope 傳遞

var app = myApp ; //angular.module('myApp', ['ngRoute','ui.bootstrap']);


app.filter('startFrom', function() { //搜尋  分頁用 | startFrom (inupt來自pipe)  start參數)
    return function(input, start) {
        if(input) {
            start = +start; //parse to int
            return input.slice(start); // 將pipe來的陣列 以start 切割開
        }
        return [];
    }
});



// 自定 rest full api service 
app.factory("myservices", ['$http', function($http) {

  var serviceBase = 'services/'  ;   // URL 路徑 指向 /php/CRUD/services/api.php
    var obj = {};
  
   obj.login = function(){
        return $http.post(serviceBase + "login"  , singin ).then(function (results) {
         return results;  // userID 
     });
     }
   obj.loginout = function(){
           return $http.get(serviceBase + "logout?u=" + userID );
     }  
   
// R 查詢  all   
   obj.getNCUT_Phones = function(){
          return $http.get(serviceBase + "getphones" );
    }
// R 查詢 by ID
   obj.getNCUT_Phone = function(phoenID){
        return $http.get(serviceBase + "getphone?id=" + phoenID );
    }
// C 新增    
    obj.insertNCUT_Phone = function (phones) {
    return $http.post(serviceBase + 'insertNCUT_Phone', phones).then(function (results) {
        return results;
    });
  };

// U 編輯更新 
  obj.updateNCUT_Phone = function (id,phones) {
      return $http.post(serviceBase + 'updateNCUT_Phone', {id:id, dataset:phones}).then( function (status) {
          return status.data;
      });
  };
// D 刪除
  obj.deleteNCUT_Phone = function (id) {
      return $http.delete(serviceBase + 'deleteNCUT_Phone?id=' + id).then( function (status) {
          return status.data;
      });
  };


    return obj;   
}]); 



app.controller('phoneCtrl', function ($scope, myservices) {
    
 $scope.deptnames = [ 
  {deptname:"全校" } ,
  {deptname:"校長室" } ,
  {deptname:"秘書室" } ,
  {deptname:"學生事務處" } ,
  {deptname:"教務處" } ,
  {deptname:"總務處" } ,
  {deptname:"研究發展處" } ,
  {deptname:"電算中心" },
{deptname:"創辦人辦公室" },
{deptname:"進修推廣部" },
{deptname:"附設進修學院" },
{deptname:"體育室" },
{deptname:"語言中心" },
{deptname:"進修學院及專校" },
{deptname:"人事室" },
{deptname:"諮商輔導中心" },

{deptname:"產學營運總中心" },

{deptname:"圖書館" },
{deptname:"主計室" },
{deptname:"國際事務處" },

{deptname:"基礎通識教育中心" },
{deptname:"博雅通識教育中心" },
{deptname:"化工與材料工程系" },
{deptname:"機械工程系" },
{deptname:"冷凍空調與能源系" },
{deptname:"精密製造科技研究所" },

{deptname:"電機工程系" },
{deptname:"電子工程系" },
{deptname:"資訊工程系" },
 

 {deptname:"文化創意事業系" },
{deptname:"景觀系" },
{deptname:"應用英語系" },

   {deptname:"工業工程與管理系" },
{deptname:"企業管理系" },
{deptname:"資訊管理系" },
  
{deptname:"流通管理系" },
{deptname:"休閒產業管理系" },
   
{deptname:"通識教育學院" },
   

 

{deptname:"系" },

{deptname:"專案管理研究所" },
 
{deptname:"學院" },
{deptname:"電資學院" },
{deptname:"工程學院" },
{deptname:"人文創意學院" },
{deptname:"管理學院" },
{deptname:"會議室" },
{deptname:"所" },
{deptname:"中心" },
{deptname:"主任" },

{deptname:"aaaa" }

 ];

$scope.searchDeptname="全校" ;
 $scope.showsearch="全校" ;

 $scope.gridOptions1 = {
    paginationPageSizes: [25, 50, 75],
    paginationPageSize: 25,
    columnDefs: [
      { name: 'name' },
      { name: 'gender' },
      { name: 'company' }
    ]
  };
//    $scope.gridOptions1.data = data;

$scope.seekdeptment = function(){
   var detpname = $scope.searchDeptname ;
   $scope.showsearch = ""+  detpname ;
   if ( detpname !="全校" ){
      $scope.search = ""+  detpname ;
   }else{
      $scope.search = ""  ;
   }
  
  // $scope.currentPage = 1;
}

   myservices.getNCUT_Phones( ).then(function(data){  //     'ajax/getQaData.php' 
        $scope.phones = data.data;
        $scope.directionLinks = true ; // 分頁器邊界
        $scope.boundaryLinks = true ; // 分頁器邊界
    

        //$scope.gridOptions1.data = data.data;
        $scope.predicate = 'pid'    ; // 排序欄位
//        $scope.currentPage = 1; //current page 與 UI bind
        //$scope.myPagination= 1;
    //    $scope.entryLimit = 5; //max no of items to display in a page 1500 = 100page
   //     $scope.oldEntryLimit = $scope.entryLimit;
// $scope.myPagination = {} ;
   //     var eachPagesize =    $scope.entryLimit ;
        var filteredItems   = $scope.phones.length  ;  //過濾後的資料量
     //   var pagelimit = filteredItems /eachPagesize ; 
     // $scope.pagelimits =  Math.ceil (pagelimit); //計算初始的頁數
      //$scope.pages = [] ;
   // for (var i = 1; i <= 200  ; i++) {
   //         var page = { id : i } ;
   //          $scope.pages.push( page );
   //     }
   //    //$scope.pagelimits = 0 ; // 顯示換頁按鈕 控制器ng-show= $scope.ispage(pageno) ;        
      
        $scope.adminauth = $rootScope.app_adminauth ;// false ; // 管理者頁面 開關
      
        // 分頁初值
        // 與slelction bind entryLimit
        $scope.filteredItems = $scope.phones.length; //Initially for no filter  
        $scope.totalItems = $scope.phones.length;
        $scope.mymessage = "allenCtrl getNCUT_Phones ok" ;

    });

   // 2016 06 16 
   /*
      
      var eachPagesize =    $scope.entryLimit ;
      var totalites =    $scope.phones.length;  
   */ 

  // $scope.ispage =function(pageNo) {
  //   //$scope.pagelimits = 10 ; // 顯示換頁按鈕 控制器ng-show= $scope.ispage(pageno) ;        
  //     pageno = +pageNo // int

  //     if ( $scope.pagelimits >= pageno )    
  //       return true ;
  //     else
  //      return false ;
  //     //return true ;
  // }
  //   $scope.setCurrent = function(pageNo) {
//      $scope.mymessage = "setCurrent Page : " +pageNo  ;  
 //   };
 
//     $scope.setPage = function(pageNo) { //setPage(page)
// /*
//   <select ng-model="searchDeptname" class="form-control">
//                 <option ng-repeat="data in deptnames" >{{data.deptname}}</option>
// </select>

// */
//       // startFrom = ( pageNo -1)* oldEntryLimit  
// //track by tracker(pageNumber, $index) 
       
//        var eachPagesize =    $scope.entryLimit ;  /// 連動 ng-repeat
//        var filteredItems   = $scope.filtered.length  ;  //過濾後的資料量
//        var pagelimit = filteredItems /eachPagesize ; 
    
//       $scope.oldEntryLimit = $scope.entryLimit;

//     $scope.pagelimits =  Math.ceil (pagelimit);

//       if ( pageNo  < $scope.pagelimits ) // 目
//       {
//       $scope.currentPage = pageNo;
//              $scope.myPagination = pageNo;
//       }  else {
//                $scope.myPagination = $scope.pagelimits ;
//         $scope.currentPage = $scope.pagelimits ;
//       }
        

//       $scope.mymessage = "setPage " + $scope.currentPage  ;  

//     };
    $scope.filter = function() {
         //$scope.mymessage = "search =" + $scope.search   ;  
            $scope.showsearch =  $scope.search   ;  
        // $scope.filteredItems = $scope.filtered.length   ;
         $timeout(function() {        
          $scope.filteredItems = $scope.filtered.length;  
          $scope.mymessage = "timeout   ok" + $scope.filteredItems  ;             }, 10);
    };

    $scope.sort_by = function(predicate) {
      $scope.mymessage = "sort_by ="   ;    
        $scope.predicate = predicate; // 指定排序欄位
        $scope.reverse = !$scope.reverse; //反向排序
    };
//parseInt
/*

Math.round、Math.floor、Math.ceil 


  Math.round 會對 JavaScript 的除法結果進行四捨五入計算。

  Math.floor 會取得小於或等於＂運算結果小數＂的最大整數。
  Math.ceil 會取得大於或等於＂運算結果小數＂的最小整數。


*/
    // pipe | 
    // ng-repeat="data in filtered = ( list | filter:search | orderBy : predicate :reverse  ) 
    //                               | startFrom:(currentPage-1)*entryLimit   repeat起點
    //                               | limitTo:entryLimit"  repeat終止


    

});

//, myphones
app.controller('editPhoneCtrl', function ($scope, $rootScope, $location, $routeParams, myservices, editphone) {
   
    var phoneID = ($routeParams.phoneID) ? parseInt($routeParams.phoneID) : 0;


    $rootScope.title = (phoneID > 0) ? 'Edit Phone' : 'Add Phone';
    $scope.buttonText = (phoneID > 0) ? 'Update Phone' : 'Add New Phone';
      var original = editphone.data;
      original.pid = phoneID;
      $scope.myphones = angular.copy(original);
      $scope.myphones.pid = phoneID;

               
    //           getNCUT_Phone
  //$scope.loaddata = 
      $scope.isClean = function() {
        return angular.equals(original, $scope.myphones);
      }

      $scope.deletePhone = function(myphones) {
        $location.path('/');
        if(confirm("Are you sure to delete Phone: "+ myphones.phone)==true)
        myservices.deleteNCUT_Phone(myphones.pid);
      };

      $scope.savePhone = function(myphones) {
        $location.path('/');
        if (phoneID <= 0) {
            myservices.insertNCUT_Phone(myphones);
        }
        else {
            myservices.updateNCUT_Phone(phoneID, myphones);
        }
    };
});

 


// app.run(['$location', '$rootScope', function($location, $rootScope) {
//     $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
//         $rootScope.title = current.$$route.title;
//     });
// }]);



