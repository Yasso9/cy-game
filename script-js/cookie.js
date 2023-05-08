// Ces fonctions ont été trouvé sur https://javascript.info/cookie
// Elles nous permettent de manipuler les cookies plus facilement

// returns the cookie with the given name,
// or undefined if not found

export class Cookie
{
    static getCookie (name) 
    {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    static setCookie (name, value, options = {}) 
    {
        options = {
            path: '/',
            // add other defaults here if necessary
            ...options
        };

        if (options.expires instanceof Date)
        {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options)
        {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true)
            {
                updatedCookie += "=" + optionValue;
            }
        }

        document.cookie = updatedCookie;
    }

    static deleteCookie (name)
    {
        Cookie.setCookie(name, "", {
            'max-age': -1
        })
    }
}