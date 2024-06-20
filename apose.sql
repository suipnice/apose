SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `apose`
--

-- --------------------------------------------------------

--
-- Structure de la table `annee_uni`
--

CREATE TABLE IF NOT EXISTS `annee_uni` (
  `cod_anu` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_anu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `composante`
--

CREATE TABLE IF NOT EXISTS `composante` (
  `COD_CMP` varchar(5) NOT NULL DEFAULT '',
  `LIB_CMP` varchar(100) NOT NULL DEFAULT '',
  `INT_1_EDI_DIP_CMP` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`COD_CMP`),
  KEY `comp` (`COD_CMP`(1)),
  KEY `compx` (`COD_CMP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `elp_chg_typ_heu`
--

CREATE TABLE IF NOT EXISTS `elp_chg_typ_heu` (
  `COD_ELP` varchar(12) NOT NULL,
  `COD_ANU` varchar(4) NOT NULL,
  `COD_TYP_HEU` varchar(8) NOT NULL,
  `NB_HEU_ELP` decimal(10,2) NOT NULL,
  PRIMARY KEY (`COD_ELP`,`COD_ANU`,`COD_TYP_HEU`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `elp_regroupe_lse`
--

CREATE TABLE IF NOT EXISTS `elp_regroupe_lse` (
  `cod_elp` varchar(12) NOT NULL DEFAULT '',
  `cod_lse` varchar(12) NOT NULL DEFAULT '',
  `nbr_max_elp_obl_chx` varchar(3) NOT NULL DEFAULT '',
  `nbr_min_elp_obl_chx` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_elp`,`cod_lse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `epreuve`
--

CREATE TABLE IF NOT EXISTS `epreuve` (
  `cod_epr` varchar(10) CHARACTER SET utf8 NOT NULL,
  `lib_epr` varchar(200) CHARACTER SET utf8 NOT NULL,
  `cod_nep` varchar(4) NOT NULL,
  `cod_tep` varchar(4) NOT NULL,
  PRIMARY KEY (`cod_epr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `epr_sanctionne_elp`
--

CREATE TABLE IF NOT EXISTS `epr_sanctionne_elp` (
  `cod_elp` varchar(8) CHARACTER SET utf8 NOT NULL,
  `cod_epr` varchar(10) CHARACTER SET utf8 NOT NULL,
  `cod_ses` varchar(1) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`cod_elp`,`cod_epr`,`cod_ses`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `etape`
--

CREATE TABLE IF NOT EXISTS `etape` (
  `cod_etp` varchar(6) NOT NULL DEFAULT '',
  `cod_vrs_vet` varchar(3) NOT NULL DEFAULT '',
  `lic_etp` varchar(25) NOT NULL DEFAULT '',
  `lib_etp` varchar(120) NOT NULL DEFAULT '',
  `cod_cyc` varchar(10) NOT NULL DEFAULT '',
  `cod_cmp` varchar(5) NOT NULL DEFAULT '',
  `cod_anu` varchar(4) NOT NULL,
  `daa_deb_rct_vet` varchar(4) NOT NULL,
  `daa_fin_rct_vet` varchar(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `liste_elp`
--

CREATE TABLE IF NOT EXISTS `liste_elp` (
  `cod_lse` varchar(8) NOT NULL DEFAULT '',
  `cod_typ_lse` char(1) NOT NULL DEFAULT '',
  `eta_lse` char(1) NOT NULL DEFAULT '',
  `lic_lse` varchar(25) NOT NULL DEFAULT '',
  `lib_lse` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_lse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `lse_regroupe_elp`
--

CREATE TABLE IF NOT EXISTS `lse_regroupe_elp` (
  `cod_lse` varchar(8) NOT NULL DEFAULT '',
  `cod_elp` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_lse`,`cod_elp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `table_elp`
--

CREATE TABLE IF NOT EXISTS `table_elp` (
  `cod_elp` varchar(8) NOT NULL DEFAULT '',
  `lic_elp` varchar(25) NOT NULL DEFAULT '',
  `lib_elp` varchar(80) NOT NULL DEFAULT '',
  `cod_nel` varchar(10) NOT NULL DEFAULT '',
  `cod_pel` varchar(2) NOT NULL DEFAULT '',
  `tem_adi` char(1) NOT NULL DEFAULT '',
  `tem_ado` char(1) NOT NULL DEFAULT '',
  `nbr_crd_elp` varchar(6) NOT NULL DEFAULT '',
  `lib_elp_lng` varchar(200) NOT NULL DEFAULT '',
  `nbr_vol_elp` decimal(10,1) NOT NULL,
  `cod_vol_elp` varchar(2) NOT NULL,
  `tem_mcc_elp` varchar(1) NOT NULL,
  PRIMARY KEY (`cod_elp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `table_elp_nbetu`
--

CREATE TABLE IF NOT EXISTS `table_elp_nbetu` (
  `cod_anu` varchar(4) NOT NULL DEFAULT '',
  `cod_etp` varchar(6) NOT NULL DEFAULT '',
  `cod_vrs_etp` varchar(3) NOT NULL DEFAULT '',
  `cod_elp` varchar(8) NOT NULL DEFAULT '',
  `nb_etu_ip` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_anu`,`cod_etp`,`cod_vrs_etp`,`cod_elp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `table_etape_apo`
--

CREATE TABLE IF NOT EXISTS `table_etape_apo` (
  `cod_anu` varchar(4) NOT NULL DEFAULT '',
  `cod_etp` varchar(6) NOT NULL DEFAULT '',
  `cod_vrs_vet` varchar(3) NOT NULL DEFAULT '',
  `lic_etp` varchar(25) NOT NULL DEFAULT '',
  `lib_etp` varchar(120) NOT NULL DEFAULT '',
  `cod_cyc` varchar(10) NOT NULL DEFAULT '',
  `cod_cmp` varchar(5) NOT NULL DEFAULT '',
  `nb_etu` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_anu`,`cod_etp`,`cod_vrs_vet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `table_etape_nbetu`
--

CREATE TABLE IF NOT EXISTS `table_etape_nbetu` (
  `cod_anu` varchar(4) NOT NULL DEFAULT '',
  `cod_etp` varchar(6) NOT NULL DEFAULT '',
  `cod_vrs_vet` varchar(3) NOT NULL DEFAULT '',
  `lib_etp` varchar(120) NOT NULL DEFAULT '',
  `nb_etu` varchar(6) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `type_heure`
--

CREATE TABLE IF NOT EXISTS `type_heure` (
  `COD_TYP_HEU` varchar(8) NOT NULL,
  `LIC_TYP_HEU` varchar(80) NOT NULL,
  `NUM_ORD_TYP_HEU` int(11) NOT NULL,
  PRIMARY KEY (`COD_TYP_HEU`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `vet_regroupe_lse`
--

CREATE TABLE IF NOT EXISTS `vet_regroupe_lse` (
  `cod_etp` varchar(6) NOT NULL DEFAULT '',
  `cod_vrs_vet` varchar(3) NOT NULL DEFAULT '',
  `cod_lse` varchar(8) NOT NULL DEFAULT '',
  `nbr_max_elp_obl_chx` varchar(3) NOT NULL DEFAULT '',
  `nbr_min_elp_obl_chx` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`cod_etp`,`cod_vrs_vet`,`cod_lse`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
