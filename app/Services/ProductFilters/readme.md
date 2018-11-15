Product Filters provide a way to produce and filter the storefront product display.

The filters are controlled from the Store Settings table for the selected store, so
unique filtering can be implemented or disabled as needed.

The Filters are designed to have three methods, a PRE processing stage, the actual Processing hook and a Post processing stage.

the filter engine will call each stage module once for that stage and then advance to the next stage and call each module again for the next stage.

## PreProcessing Stage

In the PreProcessing stage you typically set flags and options that may be of interest to other modules in any stage.


## Processing Stage

ideally you would carry out your business logic, extracting products based on options set in the pre-processing stage. product Objects are returned in the results array and given to the next module.


## PostProcessing Stage

In post proessing you would filter the result set so the final range of products is returned.
