
/**
 * Product:     Layered Navigation Pro for Enterprise Edition - 16/08/12
 * Package:     AdjustWare_Nav_10.4.9_10.0.0_557110
 * Purchase ID: n/a
 * Generated:   2013-04-22 06:59:44
 * File path:   js/jquery/aitoc.js
 * Copyright:   (c) 2013 AITOC, Inc.
 */
/**
 * 
 */

try 
{
    jQuery.noConflict();
}
catch (e) {}

/** Compare objects
 */
/*Object.prototype.equals = function(x2)
{
    for (p in this)
    {
        if(typeof(x[p])=='undefined') {return false;}
    }
    
    for (p in this)
    {
        if (this[p])
        {
            switch (typeof(this[p]))
            {
                case 'object':
                    if (!this[p].equals(x[p])) 
                    { 
                        return false;
                    } 
                    break;

                case 'function':
                    if (typeof(x[p])=='undefined' || (p != 'equals' && this[p].toString() != x[p].toString())) 
                    { 
                        return false; 
                    } 
                    break;

                default:
                    if (this[p] != x[p]) 
                    { 
                        return false; 
                    }
            }
        }
        else if (x[p])
        {
            return false;
        }
    }

    for (p in x)
    {
        if (typeof(this[p])=='undefined') 
        {
            return false;
        }
    }
    
    return true;
}*/