Ext.application({
    name        : 'Televisa',
    appFolder   : "./js/tvsa/",
    
    launch: function() {
        var me = this;

         function getJsonOfStore(store){
                var datar = new Array();
                var jsonDataEncode = "";
                var records = store.getRange();

                for (var i = 0; i < records.length; i++)
                    datar.push(records[i].data);
                
                return Ext.encode(datar);
            }

        TVSA = {
            _view    : null,
            resetGrid    : function(){
                var me      = this,
                    grid    = Ext.getCmp('tvsagrid'),
                    store   = grid.store;

                    store.removeAll();

            },
            save     : function(){
                var me          = this,
                    data        = [];

                    if(me.create.arguments.length > 0)
                    {
                        var data = me.create.arguments[0];
                        Ext.get(data).dom.value = me.getData();
                    }

            },

            getData  : function(){
                var me      = this,
                    grid    = Ext.getCmp('tvsagrid'),
                    store   = grid.store;

                    return getJsonOfStore(store);

            },
            create   : function(){
                var me          = this,
                    data        = [];

                    if(me.create.arguments.length > 0)
                        var data = me.create.arguments[0];

                    me._view = Ext.create('TVSA.Viewport',Ext.applyIf(data,{
                    }));
            },
            show    : function(){
                var me   = this,
                    id   = Ext.getBody();

                    if(me.show.arguments.length > 0)
                        var id = me.show.arguments[0];

                if(!me._view)
                    me.create({renderTo:id});
                else
                    me._view.show();


            },

            hide: function () {
                var me   = this;

                if(me._view)
                    me._view.hide();

            },
        	setFeed : function(url){
                var me       = this,
        		    tvsafeed = Ext.getCmp('tvsafeed');

                tvsafeed.loadData(url);
        	},
            setTemplate : function(url){
                var me       = this,
                    tvsafeed = Ext.getCmp('tvsatemplate');

                tvsafeed.loadData(url);
            }
        };

    }
});