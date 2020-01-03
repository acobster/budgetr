(ns budgetr.store
  (:require
   [cljs.reader :refer [read-string]]))


(defn persist! [app-state]
  (->> app-state
       str
       (js/localStorage.setItem "app-state")))


(defn fetch-from-local-storage []
  (some-> "app-state"
          js/localStorage.getItem
          read-string))