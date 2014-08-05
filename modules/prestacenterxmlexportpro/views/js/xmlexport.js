/**
* 2012-2014 PrestaCS, PrestaCenter - Anatoret plus s.r.o.
*
* Doplnění funkce Array.indexOf() pro verze JS < 1.6 je z utility oz.js, uvolněné pod New BSD Licence.
* (c) 2007 - now() Ondrej Zara, 1.7
*
* PrestaCenter XML Export Pro
*
* Module PrestaCenter XML Export Pro – version for PrestaShop 1.5 and 1.6
* Modul PrestaCenter XML Export Pro – verze pro PrestaShop 1.5 a 1.6
*
* PrestaCenter - modules and customization for PrestaShop
* PrestaCS - moduly, česká lokalizace a úpravy pro PrestaShop
* http://www.prestacs.cz
*
* @author    PrestaCenter <info@prestacenter.com>
* @category  others
* @package   prestacenterxmlexportpro
* @copyright 2012-2014 PrestaCenter - Anatoret plus s.r.o.
* @license   see link http://www.prestacs.cz/docs/licences/licence-prestacenter.html
*/

if (!Array.prototype.indexOf) {
        Array.prototype.indexOf = function(item, from) {
            var len = this.length;
            var i = from || 0;
            if (i < 0) { i += len; }
            for (;i<len;i++) {
                        if (i in this && this[i] === item) { return i; }
            }
            return -1;
        }
}
if (!Array.indexOf) {
        Array.indexOf = function(obj, item, from) { return Array.prototype.indexOf.call(obj, item, from); }
}



var XmlExportModule = {

	/**
	 * @var bool Vychozi nastaveni, jestli se maji checkboxy z vnorenych tabulek propojit do kaskady.
	 */
	useCascade : true,

	/**
	 * @var Array Cache, aby se nemusely pokazde hledat sousedni checkboxy pres jQuery selektory
	 * Pole poli: [ [Element masterCheckbox, Element slave1, Element slave2, ...], ...]
	 */
	cache : [],

	/**
	 * @var Array Nastavení pro zaškrtávač: [ { table: "název DB tabulky", ids: [] | {}, parentId: int }, ... ]
	 */
	lastChecked : [],

	/**
	 * @var Array ID služeb, jejichž stav byl změněn: [ Element, ... ]
	 */
	modified : [],


	/**
	 * Zaškrtne podle uloženého nastavení zadané checkboxy a pak promaže nastavení.
	 */
	checkLastUsed : function() {
		var data;
		while (data = XmlExportModule.lastChecked.shift()) {
			/* Pokud byl změněný stav celé služby, řídí se master checkboxem. Jinak se řídí uloženým nastavením z DB. */
			if (XmlExportModule.modified[data.parentId] === undefined) {
				jQuery.each(data.ids, function(key, value) {
					XmlExportModule.checkboxHandler($("[name='"+data.table+"Box[]'][value="+value+"]"));
				});
			} else if (XmlExportModule.modified[data.parentId] === true) {
				jQuery.each(XmlExportModule.info.tree.all[data.parentId], function(key, value) {
					XmlExportModule.checkboxHandler($("[name='"+data.table+"Box[]'][value="+value+"]"));
				});
			}
		}
	},


	/**
	 * Funkce na přepínání hodnoty checkboxu (uložená, aby se nemusela vytvářet pokaždé znova)
	 * @param {type} i
	 * @param Boolean val
	 * @returns Boolean
	 */
	toggle : function(i, val) { return !val; },


	/**
	 * Handler, ktery se povesi na onclick akci vsech checkboxu uvnitr hlavni tabulky
	 * Umoznuje vypnout kaskadovani pro nektere checkboxy (
	 * @param e Event
	 * @return bool
	 */
	checkboxHandler : function(e) {
		var elm, fieldset = [];
		if (e.is) {
			e.prop('checked', XmlExportModule.toggle);
			elm = e;
		} else {
			elm = $(e.target);
		}
		if (!elm.is(':checkbox')) {
			return true;
		}
/*
console.log("\n\n.... handler called ....");
console.log('current checkbox: ', elm);
console.log('current checkbox is '+(elm.prop('checked')?'checked':'not checked')+', '+(elm.prop('name') === 'checkme'?'master':'slave'));
console.log('testing selectors ... ');
console.log('upper slave: ', XmlExportModule.select.upperSlave(elm));
console.log('current master: ', XmlExportModule.select.masterOf(elm));
console.log('current slaves: ', XmlExportModule.select.slavesOf(elm));
console.log('lower master: ', XmlExportModule.select.lowerMaster(elm));
console.log('.... selectors done.');
*/
		/* vychozi nastaveni kaskadovani se da prepsat parametrem u onclick funkce */
		if (!e.is && e.data !== undefined && e.data.cascade !== undefined) {
			XmlExportModule.useCascade = e.data.cascade;
		}

		/* pokud to není řídicí checkbox */
		if (elm.prop('name') != 'checkme') {
			XmlExportModule.report(elm);
			if (XmlExportModule.useCascade)
				XmlExportModule.cascadeDown(elm);
			/* pokud to je nadřízený cbx, ukládá se jeho stav */
			if (elm.prop('name').indexOf(XmlExportModule.info.table) > -1)
				XmlExportModule.modified[elm.val()] = elm.prop('checked');
			XmlExportModule.info.update(elm);
		} else {
			fieldset = XmlExportModule.getFromCache(elm);
			XmlExportModule.command(fieldset);
			if (XmlExportModule.useCascade)
				XmlExportModule.cascadeUp(fieldset[0]);
		}

		return true;
	},


	/**
	 * Nacte skupinu checkboxu z cache. Pokud tam nejsou, ulozi je.
	 * @param Element elm Checkbox (může být master i slave)
	 * @return Array
	 */
	getFromCache : function(elm) {
/* console.log('getting cbx set from cache...'); */
		var fieldset = [], i = 0, master = this.select.masterOf(elm);

		if (master.length == 0) {
/* console.log('..nothing found'); */
			return [];
		}

		for (i = this.cache.length -1; i >= 0; i--) {
			if (this.cache[i][0].is(master)) {
/* console.log('..found in cache: ', this.cache[i]); */
				return this.cache[i];
			}
		}
		fieldset.push(master);
		var slaves = this.select.slavesOf(master);
		for (i = slaves.length -1; i >= 0; i--) {
			fieldset.push($(slaves[i]));
		}
		this.cache.push(fieldset);
/* console.log('..cbx set added to cache: ', fieldset); */
		return fieldset;
	},


	/**
	 * Zkusi najít skupiny checkboxu o jednu uroven vys i niz. Pokud najde a stav cilového cbx se lisi,
	 * zmeni jeho stav a pokracuje dal kaskadou.
	 */
	cascadeUp : function(master) {
		/* zavisly checkbox o uroven vys, kteremu patri tato podtabulka */
/* console.log('cascading up ... '); */
		var upperSlave = this.select.upperSlave(master);
		/* NB: přibližné porovnávání je úmyslné */
		if (upperSlave.length > 0 && upperSlave.prop('checked') != master.prop('checked')) {
			upperSlave.prop('checked', this.toggle);
			this.report(upperSlave);
		}
		this.info.update(upperSlave);
	},

	/**
	 * Pokud k zadanemu (zavislemu) checkboxu patri dalsi skupina o uroven niz, zmeni stav jejiho ridiciho prvku.
	 */
	cascadeDown : function(slave) {
/* console.log('cascading down ... '); */
		/* ridici checkbox z tabulky o uroven niz, ktera patri tomuto (zavislemu) checkboxu */
		var fieldset, lowerMaster = this.select.lowerMaster(slave);
		/* NB: přibližné porovnání je úmyslné */
		if (lowerMaster.length > 0 && lowerMaster.prop('checked') != slave.prop('checked')) {
			lowerMaster.prop('checked', this.toggle);
			fieldset = this.getFromCache(lowerMaster);
			this.command(fieldset);
		}
	},


	/**
	 * Vsem zavislym checkboxum (které nejsou vypnuté/disabled) nastavi hodnotu podle ridiciho.
	 */
	command : function(fieldset) {
/* console.log('setting dependant checkboxes... '); */
		var i;
		/* prvni polozka je master checkbox, vynecha se */
		for (i = fieldset.length -1; i > 0; i--) {
			if (!fieldset[i].prop('disabled')) {
				fieldset[i].prop('checked', fieldset[0].prop('checked'));
				/* zapamatování změny stavu nadřízeného cbx */
				if (fieldset[i].prop('name').indexOf(this.info.table) > -1) {
					this.modified[fieldset[i].val()] = fieldset[i].prop('checked');
				}
			}
/* console.log('..current element: ', fieldset[i]); */
			if (this.useCascade)
				this.cascadeDown(fieldset[i]);
			/* NB: info musí být až za kaskádováním dolů ! */
			this.info.update(fieldset[i]);
		}
	},


	/**
	 * Projde skupinu checkboxu, do ktere patri kliknuty prvek.
	 * Pokud jsou vsechny zavisle zaskrtnute, zaskrtne i ten ridici.
	 * Vypnuté (disabled) checkboxy se zcela ignorují, jako by tam nebyly
	 * @param Element slave Závislý checkbox
	 */
	report : function(slave) {
/* console.log('updating master checkbox... '); */
		var fieldset = [], allChecked = true, i;
		fieldset = this.getFromCache(slave);
		if (fieldset.length === 0) {
			return;
		}

		for (i = fieldset.length -1; i > 0; i--) {
			allChecked &= fieldset[i].prop('checked') || fieldset[i].prop('disabled');
		}
		if (allChecked != fieldset[0].prop('checked')) {
			fieldset[0].prop('checked', allChecked);
		}
		if (this.useCascade)
			this.cascadeUp(fieldset[0]);
	},


	/* Selektory na výběr potomka, řídicího cbx apod. */
	select : {

		/**
		 * Najde řídicí checkbox k zadanému
		 * @param Element elm
		 * @returns Element
		 */
		masterOf : function(elm) {
			if (elm.prop('name') === 'checkme') {
				return elm;
			} else {
				return elm.closest('table').find(':checkbox[name="checkme"]:first');
			}
		},


		/**
		 * Najde přímé podřízené řídicího checkboxu
		 * @param Element elm Řídicí checkbox (pokud není, najde si ho)
		 * @returns Element[]
		 */
		slavesOf : function(elm) {
			if (elm.prop('name') !== 'checkme') {
				elm = this.masterOf(elm);
			}
			return elm.closest('table').find('tbody:first > tr > td > :checkbox');
		},


		/**
		 * Najde podřízený checkbox o úroveň výš, na kterém závisí aktuální tabulka
		 * @param Element elm Checkbox z podřízené skupiny
		 * @returns Element
		 */
		upperSlave : function(elm) {
			if (elm.prop('name') !== 'checkme') {
				elm = this.masterOf(elm);
			}
			/* NB: selektor class^="details" je závislý na funkci display_action_details ! */
			return elm.parents('tr[class^="details"]').prev().find(':checkbox');
		},


		/**
		 * Najde řídicí checkbox ze závislé skupiny
		 * @param Element elm
		 * @returns Element
		 */
		lowerMaster : function(elm) {
			return elm.closest('tr').next().find(':checkbox[name="checkme"]');
		}
	},


	/* Doplňování info o počtu zaškrtnutých podřízených */
	info : {

		/** @var String Jméno checkboxů */
		name : '',

		/** @var String Název třídy prvku, do kterého se zobrazuje informace */
		class : 'XmlExportInfo',

		/** @var String Zobrazovaná hláška pro n vybraných feedů. "%u" se nahradí počtem zaškrtnutých podřízených */
		msg : '',

		/** @var String Zobrazovaná hláška pro všechny feedy vybrané. "%u" se nahradí počtem zaškrtnutých podřízených */
		msgAll : '',

		/** @var String Název DB tabulky, které checkboxy patří */
		table : '',

		/** @var Object Zapamatované zaškrtnuté checkboxy */
		tree : {},


		/**
		 * Ověří, jestli toto je prvek, kterému se má zobrazovat info
		 * @param Element elm Checkbox
		 * @returns Element Pokud existuje prvek pro info, vrátí ho. Jinak false.
		 */
		valid : function(elm) {
			var info, selector = 'i'+(this.class ? '.'+this.class : '');
			if (elm.prop('name') === 'checkme' || elm.prop('name') !== this.name) {
				return false;
			}
			info = elm.closest('tr').find(selector);
			return info.length > 0 ? info : false;
		},


		/**
		 * Nastaví počáteční hodnoty a zobrazí výchozí info podle dříve uložených dat
		 * @param Object data { String table, String name, String class, Object tree }
		 */
		init : function(data) {
			this.table = data.table;
			this.name = this.table+"Box[]";
			this.class = data.class;
			this.tree = data.tree;
			this.msg = data.msg;
			this.msgAll = data.msgAll;

			/* vstupni info */
			var i, collection = $("[name='"+this.name+"']");
			for (i = collection.length -1; i >= 0; i--) {
				this.update($(collection[i]), true);
			}
		},


		/**
		 * Zobrazí informaci o počtu zaškrt.podřízených checkboxů
		 * @param Element elm Checkbox, u něhož je infopole
		 */
		update : function (elm, firstRun) {
/* console.log('updating info...'); */
			var info = this.valid(elm), fieldset, lowerMaster, i, cnt = 0;
			if (info === false) {
/* console.log('..element is not valid:', elm); */
				return;
			}

			/* poprvé se načte počet zaškrtnutých jen ze stromu dat */
			if (firstRun) {
				cnt = elm.prop('checked') ? (this.tree.all[elm.val()] || []).length : this.tree[elm.val()] | 0;
/* console.log('..first run, checked: '+cnt); */
			/* pokud už byl stav služby jednou změněn, tak se vždy bere pouze stav služby, bez ohledu na DB. */
			} else {
				lowerMaster = XmlExportModule.select.lowerMaster(elm);
				if (lowerMaster.length > 0) {
					fieldset = XmlExportModule.getFromCache(lowerMaster);
					for (i = fieldset.length -1; i > 0; i--) {
						cnt += fieldset[i].prop('checked') * 1;
/* console.log('checking cbx ', fieldset[i],'checked: ', fieldset[i].prop('checked')); */
					}
/* console.log('..details were displayed. # checked: '+cnt) */
				/* pokud to není první spuštění, ale služba ještě nebyla rozbalena, tak při zaškrtnutí počítat všechny feedy. */
				} else {
					cnt = elm.prop('checked') ? (this.tree.all[elm.val()] || []).length : 0;
/* console.log('..details were not yet displayed. # checked (from cache): '+cnt); */
				}
			}

			info.text(cnt ? (cnt === (this.tree.all[elm.val()] || []).length ? this.msgAll : this.msg).replace(/%u/, cnt) : '');
		}

	}

}
