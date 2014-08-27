Ext.application({
    name        : 'Televisa',
    appFolder   : "./js/tvsa/",
    
    launch: function() {
        var me = this;

        

        TVSA = {
            _view    : null,
            create   : function(){
                var me          = this,
                    data        = [];

                    if(me.create.arguments.length > 0)
                        var data = me.create.arguments[0];

                    me._view = Ext.create('TVSA.Viewport',Ext.applyIf(data,{
                        id : 'TVSA.Viewport'
                    }));
            },
            show    : function(){
                var me   = this,
                    id   = Ext.getBody();

                    if(me.show.arguments.length > 0)
                        var id = me.show.arguments[0];

                if(!me._view)
                    me.create({id:id});
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