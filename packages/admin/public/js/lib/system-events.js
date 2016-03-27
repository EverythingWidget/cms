/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function (system) {
  system.events = new SystemEvents()

  function SystemEvents() {
    this.hosts = [];
    this.listeners = [];
  }
  
  SystemEvents.porototype.registerHost = function (host) {
    if(!host || !host.event){
      return throw 'host object should contain event';
    }
    
    this.hosts.push(host);
  };

})(System);