({
    baseUrl: ".",
    name: "./logic/main",
    out: "./logic/main-built.js",
    paths: { 
        jadeRuntime : '../public/lib/requirejs.plugin/jadeRuntime', 
    },
    pragmasOnSave: {
  		excludeJade : true
	  }  
})
